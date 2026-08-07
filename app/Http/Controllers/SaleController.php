<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SalesExport;
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
        $query = Sale::with([
            'product.category',
            'buyer',
            'seller'
        ]);

        if (!Auth::user()->is_admin) {
            $query->where('seller_id', Auth::id());
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query
            ->latest()
            ->get();

        $pdf = Pdf::loadView(
            'sales.pdf',
            compact('sales')
        );

        return $pdf->stream('relatorio-vendas.pdf');
    }

    public function exportExcel(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        return Excel::download(
            new SalesExport($request),
            'relatorio-vendas.xlsx'
        );
    }
}
