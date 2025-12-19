<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Listing;
use App\Services\PayOSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, PayOSService $payOsService): JsonResponse
    {
        $payload = $request->all();

        $data = $payOsService->verifyWebhook($payload);
        if (!$data) {
            Log::warning('PayOS webhook signature invalid', ['payload' => $payload]);
            return response()->json(['message' => 'invalid signature'], 400);
        }

        $orderCode = $data['orderCode'] ?? null;
        $amount = (int) ($data['amount'] ?? 0);

        if (!$orderCode) {
            return response()->json(['message' => 'orderCode missing'], 400);
        }

        $payment = Payment::where('transaction_id', $orderCode)->first();

        if (!$payment) {
            Log::warning('PayOS webhook payment not found', ['orderCode' => $orderCode]);
            return response()->json(['message' => 'not found'], 404);
        }

        if ($payment->amount != $amount) {
            $payment->update([
                'status' => 'failed',
                'notes' => 'Số tiền không khớp',
                'meta' => $data,
            ]);

            return response()->json(['message' => 'amount mismatch'], 400);
        }

        if ($payment->status === 'pending') {
            $payment->update([
                'status' => 'completed',
                'provider_ref' => $data['transactionId'] ?? $data['id'] ?? null,
                'paid_at' => now(),
                'meta' => $data,
            ]);

            // Áp gói lên tin đăng khi thanh toán thành công
            $this->applyPackageToListing($payment);
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * Áp gói đã thanh toán cho tin đăng (VIP/khác) + set ngày hết hạn
     */
    protected function applyPackageToListing(Payment $payment): void
    {
        if (!$payment->listing_id) {
            return;
        }

        $listing = Listing::find($payment->listing_id);
        if (!$listing) {
            return;
        }

        $package = $payment->package;
        if (!$package) {
            return;
        }

        $expiresAt = $package->duration_days
            ? now()->addDays($package->duration_days)
            : null;

        $listing->update([
            'package_id' => $package->id,
            'expires_at' => $expiresAt,
        ]);
    }
}

