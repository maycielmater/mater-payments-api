<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Get all payments
    public function index()
    {
        return response()->json([
            'success' => true,
            'payments' => Payment::latest()->get()
        ]);
    }

    // Create payment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_reference' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'status' => 'sometimes|in:pending,paid,failed',
            'reference_number' => 'nullable|string',
        ]);

        $payment = Payment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'payment' => $payment
        ], 201);
    }

    // View specific payment
    public function show($id)
    {
        $payment = Payment::findOrFail($id);

        return response()->json([
            'success' => true,
            'payment' => $payment
        ]);
    }

    // Update payment
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'order_reference' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0.01',
            'payment_method' => 'sometimes|string',
            'status' => 'sometimes|in:pending,paid,failed',
            'reference_number' => 'nullable|string',
        ]);

        $payment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully.',
            'payment' => $payment
        ]);
    }

    // Delete payment
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully.'
        ]);
    }
}