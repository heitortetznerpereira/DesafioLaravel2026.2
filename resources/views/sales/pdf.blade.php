<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Relatório de Vendas</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            font-size:12px;
        }

        h1{
            text-align:center;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th, td{
            border:1px solid black;
            padding:8px;
            text-align:left;
        }

        th{
            background:#eeeeee;
        }

    </style>

</head>

<body>

<h1>Relatório de Vendas</h1>

<p>

Período:

{{ request('start_date') ?? 'Início' }}

até

{{ request('end_date') ?? 'Hoje' }}

</p>

<table>

<thead>

<tr>

<th>Produto</th>

<th>Categoria</th>

<th>Comprador</th>

<th>Vendedor</th>

<th>Valor</th>

<th>Data</th>

</tr>

</thead>

<tbody>

@foreach($sales as $sale)

<tr>

<td>

{{ $sale->product->name }}

</td>

<td>

{{ $sale->product->category->name }}

</td>

<td>

{{ $sale->buyer->name }}

</td>

<td>

{{ $sale->seller->name }}

</td>

<td>

R$ {{ number_format($sale->price,2,',','.') }}

</td>

<td>

{{ $sale->created_at->format('d/m/Y') }}

</td>

</tr>

@endforeach

</tbody>

</table>

</body>

</html>
