<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaleController extends Controller
{
    //
    public function index()
    {
        $sales = Sale::where("buyer_id", Auth::id())->latest()->paginate(10);

        return view("sales.index", [
            "sales" => $sales,
        ]);
    }
}
