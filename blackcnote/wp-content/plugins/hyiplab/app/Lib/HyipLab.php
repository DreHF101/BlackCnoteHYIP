<?php

namespace Hyiplab\Lib;

use Hyiplab\Models\Invest;
use Hyiplab\Models\Referral;
use Hyiplab\Models\TimeSetting;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\ScheduleInvest;
use Hyiplab\Models\Holiday;
use Carbon\Carbon;

class HyipLab
{
    /**
    * Instance of investor user
    *
    * @var object
    */
    private $user;

    /**
    * Plan which is purchasing
    *
    * @var object
    */
    private $plan;

    /**
    * Set some properties
    *
    * @param object $user
    * @param object $plan
    * @return void
    */
    public function __construct($user, $plan)
    {
        $this->user = $user;
        $this->plan = $plan;
    }

    /**
    * Invest process
    *
    * @param float $amount
    * @param string $wallet
    * @return void
    */
    public function invest($amount, $wallet, $compoundTimes = 0){

        $plan = $this->plan;
        $user = $this->user;
        $afterBalance = hyiplab_balance($user->ID, $wallet) - $amount;
        update_user_meta($user->ID,"hyiplab_$wallet", $afterBalance);

        $total_invest = get_user_meta($user->ID, 'hyiplab_total_invest', true) ?? 0 + $amount;
        update_user_meta($user->ID, 'hyiplab_total_invest', $total_invest);

        $trx                        = hyiplab_trx();
        $transaction                = new Transaction();
        $transaction->user_id       = $user->ID;
        $transaction->amount        = $amount;
        $transaction->post_balance  = $afterBalance;
        $transaction->charge        = 0;
        $transaction->trx_type      = '-';
        $transaction->details       = esc_html__('Invested on ', HYIPLAB_PLUGIN_NAME) . $plan->name;
        $transaction->trx           = $trx;
        $transaction->wallet_type   = $wallet;
        $transaction->remark        = 'invest';
        $transaction->created_at    = current_time('mysql');
        $transaction->save();

        $time = get_hyiplab_time_setting($plan->time_setting_id);
        //start
        if ($plan->interest_type == 1) {
            $interestAmount = ($amount * $plan->interest) / 100;
        } else {
            $interestAmount = $plan->interest;
        }

        $period = ($plan->lifetime == 1) ? -1 : $plan->repeat_time;
        $next = self::nextWorkingDay($time->time);

        $shouldPay = -1;
        if ($period > 0) {
            $shouldPay = $interestAmount * $period;
        }

        $invest                     = new Invest();
        $invest->user_id            = $user->ID;
        $invest->plan_id            = $plan->id;
        $invest->amount             = $amount;
        $invest->interest           = $interestAmount;
        $invest->period             = $period;
        $invest->time_name          = $time->name;
        $invest->hours              = $time->time;
        $invest->next_time          = $next;
        $invest->should_pay         = $shouldPay;
        $invest->status             = 1;
        $invest->wallet_type        = $wallet;
        $invest->capital_status     = $plan->capital_back;
        $invest->trx                = $trx;
        $invest->compound_times     = $compoundTimes ?? 0;
        $invest->rem_compound_times = $compoundTimes ?? 0;
        $invest->hold_capital       = $plan->hold_capital;
        $invest->created_at         = current_time('mysql');
        $invest->save();

        if (get_option('hyiplab_invest_commission') == 1) {
            $commissionType = 'invest_commission';
            self::levelCommission($user, $amount, $commissionType, $trx);
        }

        hyiplab_notify($user, 'INVESTMENT', [
            'trx'             => $trx,
            'amount'          => hyiplab_show_amount($amount),
            'plan_name'       => $plan->name,
            'interest_amount' => hyiplab_show_amount($interestAmount),
            'time'            => $plan->lifetime == 1 ? 'lifetime' : $plan->repeat_time.' times',
            'time_name'       => $time->name,
            'wallet_type'     => vlKeyToTitle($wallet),
            'post_balance'    => hyiplab_show_amount($afterBalance),
        ]);

    }

    public static function saveScheduleInvest($request, $user_ID)
    {
        $wallet = $request->wallet_type;
        $afterBalance = hyiplab_balance($user_ID, $wallet) - $request->amount;
        update_user_meta($user_ID,"hyiplab_$wallet",$afterBalance);

        $total_invest = get_user_meta($user_ID, 'hyiplab_total_invest', true) ?? 0 + $request->amount;
        update_user_meta($user_ID, 'hyiplab_total_invest', $total_invest);

        $scheduleInvest                     = new ScheduleInvest();
        $scheduleInvest->user_id            = $user_ID;
        $scheduleInvest->plan_id            = $request->plan_id;
        $scheduleInvest->wallet             = $request->wallet_type;
        $scheduleInvest->amount             = $request->amount;
        $scheduleInvest->schedule_times     = $request->schedule_times;
        $scheduleInvest->rem_schedule_times = $request->schedule_times;
        $scheduleInvest->interval_hours     = $request->hours;
        $scheduleInvest->compound_times     = $request->compound_interest ?? 0;
        $scheduleInvest->next_invest        = hyiplab_date()->addHours($request->hours)->toDateTime();
        $scheduleInvest->created_at         = current_time('mysql');
        $scheduleInvest->updated_at         = current_time('mysql');
        $scheduleInvest->save();

        $trx                       = hyiplab_trx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user_ID;
        $transaction->amount       = $request->amount;
        $transaction->post_balance = $afterBalance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = esc_html__('Schedule Investment', HYIPLAB_PLUGIN_NAME);
        $transaction->trx          = $trx;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'schedule_invest';
        $transaction->created_at   = current_time('mysql');
        $transaction->save();

        if ( get_option('hyiplab_invest_commission') == 1) {
            $commissionType = 'invest_commission';
            self::levelCommission(get_userdata($user_ID), $request->amount, $commissionType, $trx);
        }

    }

        /**
     * Get the next working day of the system
     *
     * @param integer $hours
     * @return string
     */
    public static function nextWorkingDay($hours)
    {
        $now     = hyiplab_date();
        $next    = null;
        $i = 0;
        while (0 == 0) {
            $nextPossible = Carbon::parse($now->toDateTime())->addHours($hours)->toDateTimeString();
            if (!self::isHoliDay($nextPossible, get_option('hyiplab_off_days'))) {
                $next = $nextPossible;
                break;
            }
            $now = $now->addDays(1);
            $i++;
        }
        return $next;
    }


        /**
     * Check the date is holiday or not
     *
     * @param string $date
     * @param object $setting
     * @return string
     */
    public static function isHoliDay($date, $off_day)
    {
        $isHoliday = true;
        $dayName   = strtolower(date('D', strtotime($date)));
        $holiday   = Holiday::where('date', date('Y-m-d', strtotime($date)))->count();
        $offDay    = (array) $off_day;
        if (!array_key_exists($dayName, $offDay)) {
            if ($holiday == 0) {
                $isHoliday = false;
            }
        }

        return $isHoliday;

    }


    /**
    * Give referral commission
    *
    * @param object $user
    * @param float $amount
    * @param string $commissionType
    * @param string $trx
    * @param object $setting
    * @return void
    */
    public static function levelCommission($user, $amount, $commissionType, $trx){

        $meUser = $user;
        $i = 1;
        $level = Referral::where('commission_type', $commissionType)->count();

        $transactions = [];

        while ($i <= $level) {

            $me = $meUser;

            $refer = getViserReferrer($me->ID);

            if ($refer == 0) {
                break;
            }

            $commission = Referral::where('commission_type',$commissionType)->where('level', $i)->first();

            if (!$commission) {
                break;
            }

            $com = ($amount * $commission->percent) / 100;
            $afterBalance = hyiplab_balance($refer, 'interest_wallet') + $com;
            update_user_meta($refer, "hyiplab_interest_wallet", $afterBalance);

            $transactions = [
                'user_id' => $refer,
                'amount' => $com,
                'post_balance' => $afterBalance,
                'charge' => 0,
                'trx_type' => '+',
                'details' => esc_html__('level '.$i.' Referral Commission From ',HYIPLAB_PLUGIN_NAME) . $user->user_login,
                'trx' => $trx,
                'wallet_type' =>  'interest_wallet',
                'remark'=>'referral_commission',
                'created_at' => current_time('mysql')
            ];

            Transaction::insert($transactions);

            $refUser = get_userdata($refer);

            hyiplab_notify($refUser, 'REFERRAL_COMMISSION', [
                'amount' => hyiplab_show_amount($com),
                'post_balance' => hyiplab_show_amount($afterBalance),
                'trx' => $trx,
                'level' => ordinal($i),
                'type' => vlKeyToTitle($commissionType)
            ]);

            $team_invest = get_user_meta($refUser->ID, 'hyiplab_team_invest', true) ?? 0 + $amount;
            update_user_meta($refUser->ID, 'hyiplab_team_invest', $team_invest);

            $meUser = $refUser;
            $i++;
        }
    }

}
