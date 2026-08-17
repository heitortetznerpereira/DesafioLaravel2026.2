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

        Log::info("PagBank webhook", $payload);

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
                    "message" => "reference_id inválido.",
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

        /*
         * Payment was approved.
         */
        if ($status === "PAID") {
            DB::transaction(function () use ($sale) {
                $sale->refresh();

                /*
                 * Prevent duplicate credit.
                 */
                if ($sale->status === "paid") {
                    return;
                }

                $sale->update([
                    "status" => "paid",
                ]);

                if ($sale->seller_id) {
                    $seller = $sale->seller()->lockForUpdate()->first();

                    if ($seller) {
                        $total = $sale->unit_price * $sale->amount;

                        $seller->increment("balance", $total);
                    }
                }
            });

            return response()->json([
                "message" => "Pagamento confirmado.",
            ]);
        }

        /*
         * Payment was declined/cancelled.
         */
        if (in_array($status, ["DECLINED", "CANCELED"])) {
            $sale->update([
                "status" => "cancelled",
            ]);

            return response()->json([
                "message" => "Pagamento recusado/cancelado.",
            ]);
        }

        /*
         * Payment is still being processed.
         */
        return response()->json([
            "message" => "Pagamento ainda pendente.",
        ]);
    }
}
