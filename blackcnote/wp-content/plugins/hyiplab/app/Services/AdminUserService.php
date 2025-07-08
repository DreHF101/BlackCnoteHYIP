<?php

namespace Hyiplab\Services;

use Hyiplab\Models\User;
use Hyiplab\Models\Transaction;
use WP_Error;

class AdminUserService
{
    public function getUsers(array $filters = [], int $paginate = 20)
    {
        $query = User::query();

        if (!empty($filters['search'])) {
            $searchTerm = urldecode($filters['search']);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('user_login', $searchTerm)
                  ->orWhere('user_email', $searchTerm);
            });
        }

        if (!empty($filters['status'])) {
            $query->whereHas('meta', function ($q) use ($filters) {
                $q->where('meta_key', 'hyiplab_user_active')
                  ->where('meta_value', $filters['status']);
            });
        }
        
        if (!empty($filters['kyc_status'])) {
             $query->whereHas('meta', function ($q) use ($filters) {
                $q->where('meta_key', 'hyiplab_kyc')
                  ->where('meta_value', $filters['kyc_status']);
            });
        }

        if (!empty($filters['kyc_unverified'])) {
             $query->whereHas('meta', function ($q) {
                $q->where('meta_key', 'hyiplab_kyc')
                  ->where('meta_value', '!=', '1');
            });
        }

        return $query->orderBy('id', 'DESC')->paginate($paginate);
    }

    public function updateUser(int $userId, array $data): bool
    {
        $userData = [
            'ID'           => $userId,
            'display_name' => sanitize_text_field($data['display_name']),
        ];

        $result = wp_update_user($userData);
        if (is_wp_error($result)) {
            return false;
        }

        $countryData = $this->getCountryData();
        $countryCode = $data['country'];
        $country = $countryData[$countryCode]->country ?? '';
        $dialCode = $countryData[$countryCode]->dial_code ?? '';

        update_user_meta($userId, 'hyiplab_mobile', sanitize_text_field($dialCode . $data['mobile']));
        update_user_meta($userId, 'hyiplab_country_code', sanitize_text_field($countryCode));
        update_user_meta($userId, 'hyiplab_country', sanitize_text_field($country));
        update_user_meta($userId, 'hyiplab_address', sanitize_text_field($data['address']));
        update_user_meta($userId, 'hyiplab_city', sanitize_text_field($data['city']));
        update_user_meta($userId, 'hyiplab_state', sanitize_text_field($data['state']));
        update_user_meta($userId, 'hyiplab_zip', sanitize_text_field($data['zip']));

        return true;
    }

    public function addSubBalance(int $userId, float $amount, string $wallet, string $operation, string $remark): bool
    {
        $opAmount = ($operation === 'add') ? $amount : -$amount;
        $trxType = ($operation === 'add') ? '+' : '-';

        $afterBalance = hyiplab_balance_update($userId, $opAmount, $wallet);
        
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge = 0;
        $transaction->trx_type = $trxType;
        $transaction->details = $remark;
        $transaction->trx = hyiplab_trx();
        $transaction->wallet_type = $wallet;
        $transaction->remark = strtolower($operation) . '_balance';
        $transaction->save();

        return true;
    }

    public function getCountryData(): array
    {
        $json = file_get_contents(HYIPLAB_ROOT . 'views/partials/country.json');
        return json_decode($json, true);
    }
} 