<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Sale::with([
            'product.category',
            'buyer',
            'seller'
        ]);

        if (!Auth::user()->is_admin) {
            $query->where('seller_id', Auth::id());
        }

        if ($this->request->filled('start_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $this->request->start_date
            );
        }

        if ($this->request->filled('end_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $this->request->end_date
            );
        }

        return $query
            ->latest()
            ->get()
            ->map(function ($sale) {

                return [

                    $sale->product->name,

                    $sale->product->category->name,

                    $sale->buyer->name,

                    $sale->seller->name,

                    $sale->price,

                    $sale->created_at->format('d/m/Y'),

                ];

            });
    }

    public function headings(): array
    {
        return [

            'Produto',

            'Categoria',

            'Comprador',

            'Vendedor',

            'Valor',

            'Data',

        ];
    }
}
