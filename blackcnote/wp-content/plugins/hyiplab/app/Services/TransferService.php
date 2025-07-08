<?php

namespace Hyiplab\Services;

use Hyiplab\BackOffice\Request;
use Hyiplab\Models\Transaction;
use WP_User;
use WP_Error;

class TransferService
{
    public function transfer(WP_User $sender, Request $request): bool|WP_Error
    {
        $receiver = get_user_by('login', $request->username);

        if ($sender->ID === $receiver->ID) {
            return new WP_Error('transfer_to_self', 'You cannot transfer balance to your own account.');
        }

        $amount = (float) $request->amount;
        $wallet = $request->wallet;

        if ($amount > hyiplab_balance($sender->ID, $wallet)) {
            return new WP_Error('insufficient_balance', 'You do not have sufficient balance for this transfer.');
        }

        // Perform the transfer
        hyiplab_balance_update($sender->ID, -$amount, $wallet);
        hyiplab_balance_update($receiver->ID, $amount, $wallet);

        // Record transactions
        $this->recordSenderTransaction($sender->ID, $receiver->user_login, $amount, $wallet);
        $this->recordReceiverTransaction($receiver->ID, $sender->user_login, $amount, $wallet);

        return true;
    }

    protected function recordSenderTransaction(int $userId, string $receiverUsername, float $amount, string $wallet): void
    {
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $amount;
        $transaction->post_balance = hyiplab_balance($userId, $wallet);
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = sprintf('Balance transfer to %s', $receiverUsername);
        $transaction->trx = hyiplab_trx();
        $transaction->wallet_type = $wallet;
        $transaction->remark = 'balance_transfer';
        $transaction->save();
    }

    protected function recordReceiverTransaction(int $userId, string $senderUsername, float $amount, string $wallet): void
    {
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $amount;
        $transaction->post_balance = hyiplab_balance($userId, $wallet);
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = sprintf('Balance received from %s', $senderUsername);
        $transaction->trx = hyiplab_trx();
        $transaction->wallet_type = $wallet;
        $transaction->remark = 'balance_transfer';
        $transaction->save();
    }
} 