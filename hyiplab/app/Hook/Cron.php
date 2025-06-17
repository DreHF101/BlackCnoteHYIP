<?php

namespace Hyiplab\Hook;

use Hyiplab\Lib\HyipLab;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Plan;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\User;
use Hyiplab\Models\UserRanking;
use Hyiplab\Models\StakingInvest;
use Hyiplab\Models\ScheduleInvest;

class Cron
{
    public function cron()
    {
    
        $now = current_time('mysql');
        $off_day = get_option('hyiplab_off_days');

        $day    = strtolower(date('D'));
        $offDay = (array) $off_day;
        if (array_key_exists($day, $offDay)) {
            echo "Holiday";
            exit;
        }

        update_option('hyiplab_last_cron_run', $now);

        $invests = Invest::where('status', 1)->where('next_time', '<=', $now)->orderBy('last_time')->limit(100)->get();
        foreach ($invests as $invest) {
            $updateInvest                    = [];
            $updateInvest['return_rec_time'] = $invest->return_rec_time + 1;
            $updateInvest['paid']            = $invest->paid + $invest->interest;
            $updateInvest['should_pay']      = $invest->should_pay - ($invest->period > 0 ? $invest->interest : 0);
            $updateInvest['next_time']       = HyipLab::nextWorkingDay($invest->hours);
            $updateInvest['last_time']       = current_time('mysql');

            $afterBalance = hyiplab_balance($invest->user_id, 'interest_wallet') + $invest->interest;
            update_user_meta($invest->user_id, "hyiplab_interest_wallet", $afterBalance);

            $plan = Plan::where('id', $invest->plan_id)->first();
            if (!$plan) {
                continue;
            }
            $user = User::where('ID', $invest->user_id)->first();
            if (!$user) {
                continue;
            }
            $trx          = hyiplab_trx();
            $transactions = [
                'user_id'      => $invest->user_id,
                'invest_id'    => $invest->id,
                'amount'       => $invest->interest,
                'post_balance' => $afterBalance,
                'charge'       => 0,
                'trx_type'     => '+',
                'details'      => hyiplab_show_amount($invest->interest) . ' ' . hyiplab_currency('text') . esc_html__(' interest from ', HYIPLAB_PLUGIN_NAME) . esc_html($plan->name),
                'trx'          => $trx,
                'wallet_type'  => 'interest_wallet',
                'remark'       => 'interest',
                'created_at'   => current_time('mysql')
            ];
            Transaction::insert($transactions);

            if (get_option('hyiplab_invest_return_commission') == 1) {
                $commissionType = 'invest_return_commission';
                HyipLab::levelCommission($user, $invest->interest, $commissionType, $trx);
            }

            if ($invest->return_rec_time >= $invest->period && $invest->period != -1) {
                $updateInvest['status'] = 0;

                if ($invest->capital_status == 1  && !$invest->hold_capital) {
                    $capital      = $invest->amount;
                    $afterBalance = hyiplab_balance($invest->user_id, 'interest_wallet') + $capital;
                    update_user_meta($invest->user_id, "hyiplab_interest_wallet", $afterBalance);

                    $transactions = [
                        'user_id'      => $invest->user_id,
                        'invest_id'    => $invest->id,
                        'amount'       => $capital,
                        'post_balance' => $afterBalance,
                        'charge'       => 0,
                        'trx_type'     => '+',
                        'details'      => hyiplab_show_amount($capital) . ' ' . hyiplab_currency('text') . esc_html__('capital back from', HYIPLAB_PLUGIN_NAME) . esc_html($plan->name),
                        'trx'          => $trx,
                        'wallet_type'  => 'interest_wallet',
                        'remark'       => 'interest',
                        'created_at'   => current_time('mysql')
                    ];

                    Transaction::insert($transactions);
                }
            }

            if ($invest->rem_compound_times) {
                $interest        = $invest->interest;
                $newInvestAmount = $invest->amount + $interest;
                $newInterest     = $invest->interest * $newInvestAmount / $invest->amount;
                $newShouldPay    = $invest->should_pay == -1 ? -1 : ($invest->period - $invest->return_rec_time) * $newInterest;

                $afterBalance = hyiplab_balance($invest->user_id, 'interest_wallet') - $invest->interest;
                update_user_meta($invest->user_id, "hyiplab_interest_wallet", $afterBalance);

                $updateInvest['amount']              = $newInvestAmount;
                $updateInvest['interest']            = $newInterest;
                $updateInvest['should_pay']          = $newShouldPay;
                $updateInvest['rem_compound_times'] -= 1;

                $transaction               = new Transaction();
                $transaction->user_id      = $invest->user_id;
                $transaction->invest_id    = $invest->id;
                $transaction->amount       = $interest;
                $transaction->post_balance = $afterBalance;
                $transaction->charge       = 0;
                $transaction->trx_type     = '-';
                $transaction->details      = 'Invested Compound on ' . $plan->name;
                $transaction->trx          = $trx;
                $transaction->wallet_type  = 'interest_wallet';
                $transaction->remark       = 'invest_compound';
                $transaction->created_at   = current_time('mysql');
                $transaction->save();
            }

            Invest::where('id', $invest->id)->update($updateInvest);

            $plan = get_hyiplab_plan($invest->plan_id);

            hyiplab_notify($user, 'INTEREST', [
                'trx'          => $trx,
                'amount'       => hyiplab_show_amount($invest->interest),
                'plan_name'    => esc_html($plan->name),
                'post_balance' => hyiplab_show_amount($afterBalance),
            ]);
        }
    }

    public function rank()
    {
        if (!get_option('hyiplab_user_ranking')) {
            return 'MODULE DISABLED';
        }

        $args = array(
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'hyiplab_last_rank_update',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => 'hyiplab_last_rank_update',
                    'compare' => 'NOT EXISTS',
                ),
            ),
            'orderby'  => 'meta_value',
            'order'    => 'desc',
            'number' => 50
        );
        $user_query = new \WP_User_Query($args);
        $users = $user_query->get_results();

        foreach ($users as $user) {

            update_user_meta($user->ID, 'hyiplab_last_rank_update', current_time('timestamp'));
            $userInvests = get_user_meta($user->ID, 'hyiplab_total_invest', true);
            $referralInvests = get_user_meta($user->ID, 'hyiplab_team_invest', true);
            $referralCount = getViserAllReferrer($user->ID, true);
            $userRankingId = get_user_meta($user->ID, 'hyiplab_ranking_id', true);

            $rankings = UserRanking::where('status', 1)->where('id', '>', $userRankingId)->where('minimum_invest', '<=', $userInvests)->where('min_referral_invest', '<=', $referralInvests)->where('min_referral', '<=', $referralCount)->get();

            foreach ($rankings as $ranking) {

                $afterBalance = hyiplab_balance($user->ID, 'interest_wallet') + $ranking->bonus;
                update_user_meta($user->ID, "hyiplab_interest_wallet", $afterBalance);
                update_user_meta($user->ID, "hyiplab_ranking_id", $ranking->id);

                $transaction               = new Transaction();
                $transaction->user_id      = $user->ID;
                $transaction->amount       = $ranking->bonus;
                $transaction->charge       = 0;
                $transaction->post_balance = $afterBalance;
                $transaction->trx_type     = '+';
                $transaction->trx          = hyiplab_trx();
                $transaction->remark       = 'ranking_bonus';
                $transaction->wallet_type  = 'interest_wallet';
                $transaction->details      = hyiplab_show_amount($ranking->bonus) . ' ' . hyiplab_currency('text') . ' ranking bonus for ' . $ranking->name;
                $transaction->created_at   = current_time('mysql');
                $transaction->save();
            }
        }
    }

    public function investSchedule()
    {
        global $wpdb;
        try {
            if (!get_option('hyiplab_schedule_invest')) {
                return 'MODULE DISABLED';
            }

            $scheduleInvests = ScheduleInvest::where('next_invest', '<=', hyiplab_date()->now())->where('rem_schedule_times', '>', 0)->where('status', 1)->get();
            
            $planIds = [];
            foreach ($scheduleInvests as $scheduleInvest) {
                if (!in_array($scheduleInvest->plan_id, $planIds)){
                    $planIds[] = $scheduleInvest->plan_id;
                }
            }

            $query = $wpdb->prepare(
                "SELECT id 
                 FROM {$wpdb->prefix}hyiplab_plans 
                 WHERE id IN (" . implode(',', $planIds) . ")
                 AND status = 1
                 AND EXISTS (
                     SELECT 1
                     FROM {$wpdb->prefix}hyiplab_time_settings
                     WHERE {$wpdb->prefix}hyiplab_time_settings.id = {$wpdb->prefix}hyiplab_plans.time_setting_id
                     AND {$wpdb->prefix}hyiplab_time_settings.status = 1
                 )"
            );
            
            $activePlanIds = $wpdb->get_col($query);

            foreach ($scheduleInvests as $scheduleInvest) {
                $userId         = $scheduleInvest->user_id;
                $user           = User::find($userId);
                $wallet         = $scheduleInvest->wallet;
                $plan           = Plan::where('id', $scheduleInvest->plan_id)->first();
                $wallet_amount  = get_user_meta($userId,"hyiplab_$wallet", true);
                $scheduleInvest = ScheduleInvest::find($scheduleInvest->id);

                if ($scheduleInvest->amount > $wallet_amount) {
                    $scheduleInvest->next_invest = hyiplab_date()->addHours($scheduleInvest->interval_hours)->toDateTime();
                    $scheduleInvest->save();

                    hyiplab_notify($userId, 'INSUFFICIENT_BALANCE', [
                        'invest_amount' => hyiplab_show_amount($scheduleInvest->amount),
                        'wallet'        => hyiplab_key_to_title($wallet),
                        'plan_name'     => $plan->name,
                        'balance'       => hyiplab_show_amount($wallet_amount),
                        'next_schedule' => $scheduleInvest->next_invest,
                    ]);
                    continue;
                }

                if (!in_array($scheduleInvest->plan_id, $activePlanIds)) {
                    continue;
                }
                
                $hyip = new HyipLab($user, $plan);
                $hyip->invest($scheduleInvest->amount, $wallet, $scheduleInvest->compound_times);

                $scheduleInvest->rem_schedule_times -= 1;
                $scheduleInvest->next_invest         = $scheduleInvest->rem_schedule_times ? hyiplab_date()->addHours($scheduleInvest->interval_hours)->toDateTime() : null;
                $scheduleInvest->status              = $scheduleInvest->rem_schedule_times ? 1 : 0;
                $scheduleInvest->save();
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
        
    }

    public static function staking()
    {
        try {
            $stakingInvests = StakingInvest::where('status', 1)->where('end_at', '<=', hyiplab_date()->now())->get();

            foreach ($stakingInvests as $stakingInvest) {
                $totalStaking = $stakingInvest->invest_amount + $stakingInvest->interest;

                $afterBalance = hyiplab_balance($stakingInvest->user_id, 'interest_wallet') + $totalStaking;
                update_user_meta($stakingInvest->user_id, "hyiplab_interest_wallet", $afterBalance);

                StakingInvest::where('id', $stakingInvest->id)->update([
                    'status' => 2,
                    'updated_at' => current_time('mysql'),
                ]);

                $transaction               = new Transaction();
                $transaction->user_id      = $stakingInvest->user_id;
                $transaction->amount       = $stakingInvest->invest_amount + $stakingInvest->interest;
                $transaction->post_balance = $afterBalance;
                $transaction->charge       = 0;
                $transaction->trx_type     = '+';
                $transaction->details      = 'Staking invested return';
                $transaction->trx          = hyiplab_trx();
                $transaction->wallet_type  = 'interest_wallet';
                $transaction->remark       = 'staking_invest_return';
                $transaction->created_at   = current_time('mysql');
                $transaction->save();
                
            }

        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

}
