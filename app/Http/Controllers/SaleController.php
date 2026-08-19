<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SalesExport;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Sale::with(["product.category", "buyer", "seller"]);

        if (!Auth::user()->is_admin) {
            $query->where("seller_id", Auth::id());
        }

        if ($request->filled("start_date")) {
            $query->whereDate("created_at", ">=", $request->start_date);
        }

        if ($request->filled("end_date")) {
            $query->whereDate("created_at", "<=", $request->end_date);
        }

        $sales = $query->latest()->paginate(10)->withQueryString();

        $salesChartLabels = [];
        $salesChartData = [];

        if (!Auth::user()->is_admin) {
            $chartQuery = Sale::query()
                ->where("seller_id", Auth::id());

            if ($request->filled("start_date")) {
                $chartQuery->whereDate("created_at", ">=", $request->start_date);
            }

            if ($request->filled("end_date")) {
                $chartQuery->whereDate("created_at", "<=", $request->end_date);
            }

            $monthExpression = DB::connection()->getDriverName() === "sqlite"
                ? "strftime('%Y-%m', created_at)"
                : "DATE_FORMAT(created_at, '%Y-%m')";

            $salesChart = $chartQuery
                ->selectRaw("$monthExpression as month, COUNT(*) as total")
                ->where("created_at", ">=", now()->subMonths(11)->startOfMonth())
                ->groupBy("month")
                ->orderBy("month")
                ->get()
                ->keyBy("month");

            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i)->startOfMonth();
                $key = $month->format("Y-m");

                $salesChartLabels[] = $month->translatedFormat("M");
                $salesChartData[] = $salesChart->get($key)?->total ?? 0;
            }
        }

        return view("sales.index", compact("sales", "salesChartLabels", "salesChartData"));
    }

    public function purchases(Request $request)
        {
            $query = Sale::with(["product.category", "buyer", "seller"]);

            $query->where("buyer_id", Auth::id());

            if ($request->filled("start_date")) {
                $query->whereDate("created_at", ">=", $request->start_date);
            }

            if ($request->filled("end_date")) {
                $query->whereDate("created_at", "<=", $request->end_date);
            }

            $sales = $query->latest()->paginate(10)->withQueryString();

            return view("sales.purchases", compact("sales"));
        }

    public function exportPdf(Request $request)
    {
        $query = Sale::with(["product.category", "buyer", "seller"]);

        if (!Auth::user()->is_admin) {
            $query->where("seller_id", Auth::id());
        }

        if ($request->filled("start_date")) {
            $query->whereDate("created_at", ">=", $request->start_date);
        }

        if ($request->filled("end_date")) {
            $query->whereDate("created_at", "<=", $request->end_date);
        }

        $sales = $query->latest()->get();

        $pdf = Pdf::loadView("sales.pdf", compact("sales"));

        return $pdf->stream("relatorio-vendas.pdf");
    }


    public function exportPdfPurchase(Request $request)
    {
        $query = Sale::with(["product.category", "buyer", "seller"]);

            $query->where("buyer_id", Auth::id());

        if ($request->filled("start_date")) {
            $query->whereDate("created_at", ">=", $request->start_date);
        }

        if ($request->filled("end_date")) {
            $query->whereDate("created_at", "<=", $request->end_date);
        }

        $sales = $query->latest()->get();

        $pdf = Pdf::loadView("sales.pdf", compact("sales"));

        return $pdf->stream("relatorio-vendas.pdf");
    }

    public function exportExcel(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        return Excel::download(
            new SalesExport($request),
            "relatorio-vendas.xlsx",
        );
    }

    public function buy(Request $request, Product $product)
    {
        $request->validate([
            "amount" => ["required", "integer", "min:1"],
        ]);

        if ($product->user_id === Auth::id()) {
            return back()->withErrors([
                "product" => "Você não pode comprar seu próprio produto.",
            ]);
        }

        $amount = $request->integer("amount");

        /*
         * Save the product information at the moment
         * of purchase.
         */
        $sale = Sale::create([
            "product_id" => $product->id,
            "buyer_id" => Auth::id(),
            "seller_id" => $product->creator_id,

            "status" => "pending",

            "image" => $product->image,
            "name" => $product->name,
            "amount" => $amount,
            "unit_price" => $product->price,
        ]);

        $response = Http::withToken(
            config("services.pagbank.token")
        )->post(
            config("services.pagbank.url") . "/checkouts",
            [
                "reference_id" => "sale-" . $sale->id,

                "items" => [
                    [
                        "reference_id" => (string) $sale->id,
                        "name" => $sale->name,
                        "quantity" => $sale->amount,
                        "unit_amount" => (int) round(
                            $sale->unit_price * 100
                        ),
                    ],
                ],

                "redirect_url" => route("sales.return", $sale),

                "return_url" => route("sales.return", $sale),

                "payment_notification_urls" => [
                    route("pagbank.webhook"),
                ],
            ]
        );

        /*
        $response = Http::withToken(config("services.pagbank.token"))->post(

            config("services.pagbank.url") . "/checkouts",
            [
                "reference_id" => "sale-" . $sale->id,

                "items" => [
                    [
                        "reference_id" => (string) $sale->id,
                        "name" => $sale->name,
                        "quantity" => $sale->amount,
                        "unit_amount" => (int) round($sale->unit_price * 100),
                    ],
                ],

                "payment_notification_urls" => [route("pagbank.webhook")],
            ],
        );
        */

        if ($response->failed()) {
            $sale->delete();
            dd([
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'json' => $response->json(),
                ]);

            return back()->withErrors([
                "payment" => $response->json(),
            ]);
        }

        $checkout = $response->json();

        $sale->update([
            "pagbank_checkout_id" => $checkout["id"],
        ]);

        $payLink = collect($checkout["links"] ?? [])->firstWhere("rel", "PAY");

        if (!$payLink) {
            $sale->delete();
            dd("Erro!");

            return back()->withErrors([
                "payment" => "O PagBank não retornou um link de pagamento.",
            ]);
        }

        return redirect()->away($payLink["href"]);
    }

    public function return(Sale $sale)
    {
        return view("sales.return", [
            "sale" => $sale,
        ]);
    }
}
