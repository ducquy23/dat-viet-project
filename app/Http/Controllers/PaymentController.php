<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Payment;
use App\Services\PayOSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request, PayOSService $payOsService): JsonResponse
    {
        $user = auth('partner')->user();

        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập'], 401);
        }

        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'listing_id' => 'nullable|exists:listings,id',
        ]);

        $package = Package::active()->findOrFail($validated['package_id']);

        $amount = (int) $package->price;
        $description = sprintf('Thanh toán gói %s cho tin đăng', $package->name);

        $paymentIntent = $payOsService->createPayment(
            $amount,
            $description,
            $request->input('return_url'),
            $request->input('cancel_url')
        );

        $payment = Payment::create([
            'user_id' => $user->id,
            'listing_id' => $validated['listing_id'] ?? null,
            'package_id' => $package->id,
            'transaction_id' => $paymentIntent['order_code'],
            'amount' => $amount,
            'currency' => 'VND',
            'payment_method' => 'bank_transfer',
            'provider' => 'payos',
            'status' => 'pending',
            'qr_url' => $paymentIntent['qr_url'],
            'checkout_url' => $paymentIntent['checkout_url'],
            'expired_at' => $paymentIntent['expired_at'],
            'payment_info' => $paymentIntent['raw'] ?? [],
        ]);

        return response()->json([
            'payment_id' => $payment->id,
            'order_code' => $payment->transaction_id,
            'qr_url' => $payment->qr_url,
            'checkout_url' => $payment->checkout_url,
            'expired_at' => $payment->expired_at,
            'status' => $payment->status,
        ]);
    }

    public function show(Payment $payment): JsonResponse
    {
        $user = auth('partner')->user();

        if (!$user || $payment->user_id !== $user->id) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        $isExpired = $payment->expired_at && now()->greaterThan($payment->expired_at) && $payment->isPending();

        return response()->json([
            'status' => $isExpired ? 'expired' : $payment->status,
            'expired_at' => $payment->expired_at,
            'amount' => $payment->amount,
            'transaction_id' => $payment->transaction_id,
            'provider_ref' => $payment->provider_ref,
        ]);
    }
}

