<?php

namespace App\Http\Controllers;

use App\Enums\Payments\PaymentStatus;
use App\Events\PaymentApproved;
use App\Events\PaymentCreated;
use App\Events\RejectPaymentEvent;
use App\Facades\ApiResponse;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentCollection;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\Interfaces\Controllers\v1\PaymentControllerInterface;

class PaymentController extends Controller implements PaymentControllerInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::paginate(config('settings.pagination.items_per_page'));
        return ApiResponse::data(new PaymentCollection($payments))
            ->message('')
            ->send();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $paymentLimitationTime = config('settings.payments.payment_creation_time_limit');
        $paymentInLimitationTime = Payment::where('user_id', $request->user_id)
            ->where('currency_key', $request->currency_key)
            ->where('created_at', '>', Carbon::now()->subMinutes($paymentLimitationTime)->toDateTimeString())
            ->exists();

        if ($paymentInLimitationTime) {
            throw new BadRequestException(__('payment.errors.payment_creation_time_limit', ['currency' => $request->currency_key, 'minute' => $paymentLimitationTime]));
        }

        if ($payment = auth()->user()->payments()->create($request->all())) {
            PaymentCreated::dispatch($payment);
            return ApiResponse::data(new PaymentResource($payment))
                ->message(__('payment.messages.create_successfull'))
                ->send(201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return ApiResponse::data(new PaymentResource($payment))
            ->message(__('payment.messages.found_successfull'))
            ->send(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        if (!in_array($payment->status, [PaymentStatus::PENDING])) {
            throw new BadRequestException(__('payment.errors.payment_cant_delete'));
        }

        $payment->delete();

        return ApiResponse::message(__('payment.messages.payment_successfully_delete'))
            ->send();
    }

    /**
     * approve the specified Payment from storage.
     */
    public function approve(Payment $payment)
    {
        if ($payment->status != PaymentStatus::PENDING) {
            throw new BadRequestException(__('payment.errors.not_pending'));
        }

        DB::beginTransaction();

        $payment->update([
            'status' => PaymentStatus::APPROVED->value,
            'status_update_at' => Carbon::now(),
            'status_update_by' => auth()->user()->id
        ]);

        $transactionOwner = User::find($payment->user_id);
        $balance = $transactionOwner->getBalance($payment->user_id);
        $transactionOwner->transactions()->lockForUpdate();
        Transaction::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'currency_key' => $payment->currency_key,
            'balance' => ($balance + $payment->amount)
        ]);

        DB::commit();

        PaymentApproved::dispatch($payment);

        return ApiResponse::data(new PaymentResource($payment))
            ->message(__('payment.messages.approve_successfull'))
            ->send(200);
    }

    /**
     * reject the specified Payment from storage.
     */
    public function reject(Payment $payment)
    {
        if ($payment->status != PaymentStatus::PENDING) {
            throw new BadRequestException(__('payment.errors.not_pending'));
        }

        $payment->update([
            'status' => PaymentStatus::REJECTED->value,
            'status_update_at' => Carbon::now(),
            'status_update_by' => auth()->user()->id
        ]);
        RejectPaymentEvent::dispatch($payment);
        return ApiResponse::data(new PaymentResource($payment))
            ->message(__('payment.messages.reject_successfull'))
            ->send(200);
    }
}
