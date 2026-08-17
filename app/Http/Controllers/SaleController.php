<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SalesExport;
use App\Models\Product;
use Laravel\Pail\ValueObjects\Origin\Http;
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

        return view("sales.index", compact("sales"));
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
        $amount = (int) $request->input("amount", 1);

        if ($amount < 1) {
            return back()->withErrors([
                "amount" => "Quantidade inválida.",
            ]);
        }

        if ($product->user_id === Auth::id()) {
            return back()->withErrors([
                "product" => "Você não pode comprar seu próprio produto.",
            ]);
        }

        $unitPrice = $product->price;
        $totalPrice = $unitPrice * $amount;

        $sale = Sale::create([
            "buyer_id" => Auth::id(),
            "seller_id" => $product->user_id,
            "product_id" => $product->id,
            "amount" => $amount,
            "unit_price" => $unitPrice,
            "total_price" => $totalPrice,
            "status" => "pending",
        ]);

        $response = Http::withToken(config("services.pagbank.token"))->post(
            config("services.pagbank.url") . "/checkouts",
            [
                "reference_id" => "sale-" . $sale->id,

                "items" => [
                    [
                        "reference_id" => (string) $product->id,
                        "name" => $product->name,
                        "amount" => $amount,
                        "unit_amount" => (int) round($unitPrice * 100),
                    ],
                ],

                "payment_notification_urls" => [route("pagbank.webhook")],

                "redirect_url" => route("sales.return", $sale),

                "return_url" => route("sales.return", $sale),
            ],
        );

        if ($response->failed()) {
            $sale->delete();

            return back()->withErrors([
                "payment" => "Não foi possível criar o checkout.",
            ]);
        }

        $checkout = $response->json();

        $sale->update([
            "pagbank_checkout_id" => $checkout["id"],
        ]);

        $payLink = collect($checkout["links"])->firstWhere("rel", "PAY");

        if (!$payLink) {
            $sale->delete();

            return back()->withErrors([
                "payment" => "O PagBank não retornou o link de pagamento.",
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
