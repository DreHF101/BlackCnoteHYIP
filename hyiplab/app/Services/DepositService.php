<?php

namespace Hyiplab\Services;

use Hyiplab\Models\Deposit;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Models\Transaction;

class DepositService
{
    public function getDeposits(array $filters = [], int $paginate = 20)
    {
        $query = Deposit::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['method_code'])) {
            $query->where('method_code', '>=', $filters['method_code']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        return $query->orderBy('id', 'desc')->paginate($paginate);
    }

    public function approveDeposit(int $depositId): Deposit
    {
        $deposit = Deposit::where('id', $depositId)->where('status', 2)->firstOrFail();
        $this->updateUserData($deposit, true);
        return $deposit;
    }

    public function rejectDeposit(int $depositId, string $message): Deposit
    {
        $deposit = Deposit::where('id', $depositId)->where('status', 2)->firstOrFail();
        $deposit->admin_feedback = sanitize_textarea_field($message);
        $deposit->status = 3; // Rejected
        $deposit->save();

        $gateway = GatewayCurrency::where('method_code', $deposit->method_code)
            ->where('currency', $deposit->method_currency)->first();
            
        $user = get_userdata($deposit->user_id);

        hyiplab_notify($user, 'DEPOSIT_REJECT', [
            'method_name'       => $gateway->name,
            'method_currency'   => $deposit->method_currency,
            'method_amount'     => hyiplab_show_amount($deposit->final_amo),
            'amount'            => hyiplab_show_amount($deposit->amount),
            'charge'            => hyiplab_show_amount($deposit->charge),
            'rate'              => hyiplab_show_amount($deposit->rate),
            'trx'               => $deposit->trx,
            'rejection_message' => $message,
        ]);

        return $deposit;
    }

    public function updateUserData(Deposit $deposit, bool $isManual = false)
    {
        if ($deposit->status == 0 || $deposit->status == 2) {
            $deposit->status = 1; // Approved
            $deposit->save();

            $afterBalance = hyiplab_balance_update($deposit->user_id, $deposit->amount, 'deposit_wallet');

            $gateway = GatewayCurrency::where('method_code', $deposit->method_code)
                ->where('currency', $deposit->method_currency)->first();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $afterBalance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = "Deposited via " . $gateway->name;
            $transaction->trx = $deposit->trx;
            $transaction->wallet_type = 'deposit_wallet';
            $transaction->remark = 'deposit';
            $transaction->save();
            
            $user = get_userdata($deposit->user_id);

            // Additional logic like commission and notification
        }
    }
} 