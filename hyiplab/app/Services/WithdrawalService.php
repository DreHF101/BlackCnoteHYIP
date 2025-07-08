<?php

namespace Hyiplab\Services;

use Hyiplab\Lib\FormProcessor;
use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Form;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\WithdrawMethod;
use Carbon\Carbon;
use Hyiplab\Log\Logger;

class WithdrawalService
{
    public function getWithdrawalMethods()
    {
        return WithdrawMethod::where('status', 1)->get();
    }

    public function checkHolidayStatus(): array
    {
        $isHoliday = HyipLab::isHoliDay(now(), get_option('hyiplab_off_days'));
        $nextWorkingDay = null;

        if ($isHoliday && !get_option('hyiplab_withdrawal_on_holiday')) {
            $nextWorkingDay = Carbon::parse(HyipLab::nextWorkingDay(24))->toDateString();
        }

        return [
            'is_holiday' => $isHoliday,
            'next_working_day' => $nextWorkingDay
        ];
    }

    public function canWithdrawToday(): bool
    {
        $holidayStatus = $this->checkHolidayStatus();
        return !($holidayStatus['is_holiday'] && !get_option('hyiplab_withdrawal_on_holiday'));
    }

    public function createWithdrawal(int $methodId, float $amount, int $userId): Withdrawal
    {
        $method = WithdrawMethod::where('id', $methodId)->where('status', 1)->firstOrFail();
        
        // Validate amount limits
        if ($amount < $method->min_limit || $amount > $method->max_limit) {
            throw new \InvalidArgumentException('Please follow the withdrawal limits.');
        }

        // Check user balance
        if ($amount > hyiplab_balance($userId, 'interest_wallet')) {
            throw new \InvalidArgumentException('You do not have sufficient balance for withdrawal.');
        }

        $charge = $method->fixed_charge + ($amount * $method->percent_charge / 100);
        $afterCharge = $amount - $charge;
        $finalAmount = $afterCharge * $method->rate;
        $trx = hyiplab_trx();

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id;
        $withdraw->user_id = $userId;
        $withdraw->amount = $amount;
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = $trx;
        $withdraw->save();

        return $withdraw;
    }

    public function submitWithdrawal(string $trx, array $formData): Withdrawal
    {
        $withdraw = Withdrawal::where('trx', $trx)->where('status', 0)->orderBy('id', 'desc')->firstOrFail();
        $method = WithdrawMethod::findOrFail($withdraw->method_id);
        $user = get_userdata($withdraw->user_id);

        // Double-check balance
        if ($withdraw->amount > hyiplab_balance($withdraw->user_id, 'interest_wallet')) {
            throw new \InvalidArgumentException('Your requested amount is larger than your current balance.');
        }

        // Process form data
        $form = Form::findOrFail($method->form_id);
        $formDataObj = json_decode(json_encode(maybe_unserialize($form->form_data)));
        $formProcessor = new FormProcessor();
        $userData = $formProcessor->processFormData($formData, $formDataObj);

        // Update withdrawal
        $withdraw->status = 2; // Pending
        $withdraw->withdraw_information = maybe_serialize($userData);
        $withdraw->save();

        // Update user balance
        $afterBalance = hyiplab_balance_update($withdraw->user_id, -$withdraw->amount, 'interest_wallet');

        // Create transaction record
        $this->createWithdrawalTransaction($withdraw, $method, $afterBalance);

        // Send notification
        hyiplab_notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => hyiplab_show_amount($withdraw->final_amount),
            'amount' => hyiplab_show_amount($withdraw->amount),
            'charge' => hyiplab_show_amount($withdraw->charge),
            'rate' => hyiplab_show_amount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => hyiplab_show_amount($afterBalance),
        ]);

        return $withdraw;
    }

    protected function createWithdrawalTransaction(Withdrawal $withdraw, WithdrawMethod $method, float $afterBalance): Transaction
    {
        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = hyiplab_show_amount($withdraw->final_amount) . ' ' . $withdraw->currency . esc_html__(' Withdraw Via ', HYIPLAB_PLUGIN_NAME) . $method->name;
        $transaction->trx = $withdraw->trx;
        $transaction->wallet_type = "interest_wallet";
        $transaction->remark = 'withdraw';
        $transaction->save();

        return $transaction;
    }

    public function getUserWithdrawals(int $userId, int $paginate = 20)
    {
        return Withdrawal::where('user_id', $userId)
            ->where('status', '!=', 0)
            ->orderBy('id', 'desc')
            ->paginate($paginate);
    }

    public function getWithdrawalByTrx(string $trx): ?Withdrawal
    {
        return Withdrawal::where('trx', $trx)->where('status', 0)->orderBy('id', 'desc')->first();
    }

    // Admin methods (existing)
    public function getWithdrawals(array $filters = [], int $paginate = 20)
    {
        $query = Withdrawal::query();

        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        return $query->orderBy('id', 'desc')->paginate($paginate);
    }

    public function approveWithdrawal(int $withdrawalId): Withdrawal
    {
        $withdrawal = Withdrawal::findOrFail($withdrawalId);
        $withdrawal->status = 1; // Approved
        $withdrawal->admin_feedback = 'Approved';
        $withdrawal->save();

        Logger::info('Withdrawal approved', ['withdrawal_id' => $withdrawalId, 'user_id' => $withdrawal->user_id]);

        return $withdrawal;
    }

    public function rejectWithdrawal(int $withdrawalId, string $reason = ''): Withdrawal
    {
        $withdrawal = Withdrawal::findOrFail($withdrawalId);
        $withdrawal->status = 3; // Rejected
        $withdrawal->admin_feedback = $reason;
        $withdrawal->save();

        // Refund the amount
        hyiplab_balance_update($withdrawal->user_id, $withdrawal->amount, 'interest_wallet');

        Logger::warning('Withdrawal rejected', ['withdrawal_id' => $withdrawalId, 'user_id' => $withdrawal->user_id, 'reason' => $reason]);

        return $withdrawal;
    }
} 