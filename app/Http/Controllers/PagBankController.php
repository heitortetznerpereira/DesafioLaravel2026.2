<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagBankController extends Controller
{
    public function webhook(Request $request)
    {
        $payload = $request->all();

        Log::info("PagBank webhook recebido", $payload);

        $referenceId = $payload["reference_id"] ?? null;

        if (!$referenceId) {
            return response()->json(
                [
                    "message" => "reference_id não encontrado.",
                ],
                400,
            );
        }

        if (!str_starts_with($referenceId, "sale-")) {
            return response()->json(
                [
                    "message" => "Referência inválida.",
                ],
                400,
            );
        }

        $saleId = str_replace("sale-", "", $referenceId);

        $sale = Sale::find($saleId);

        if (!$sale) {
            return response()->json(
                [
                    "message" => "Venda não encontrada.",
                ],
                404,
            );
        }

        $charge = $payload["charges"][0] ?? null;

        if (!$charge) {
            return response()->json(
                [
                    "message" => "Cobrança não encontrada.",
                ],
                400,
            );
        }

        $status = $charge["status"] ?? null;

        if ($status !== "PAID") {
            $sale->update([
                "status" => match ($status) {
                    "IN_ANALYSIS" => "pending",
                    "WAITING" => "pending",
                    "DECLINED" => "cancelled",
                    "CANCELED" => "cancelled",
                    default => $sale->status,
                },
            ]);

            return response()->json([
                "message" => "Status recebido.",
            ]);
        }

        DB::transaction(function () use ($sale) {
            $sale->refresh();

            // Prevent duplicate credit.
            if ($sale->status === "paid") {
                return;
            }

            $sale->update([
                "status" => "paid",
            ]);

            $seller = $sale->seller()->lockForUpdate()->firstOrFail();

            $seller->increment("balance", $sale->total_price);
        });

        return response()->json([
            "message" => "Pagamento processado.",
        ]);
    }
}
