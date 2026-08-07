<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
}
