<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Transaction;

class InvestReportController extends Controller
{
    public function dashboard()
    {
        global $wpdb;

        $pageTitle = "Investment Statistics";

        $widget['total_invest'] = Transaction::where('remark', 'invest')->sum('amount');
        $widget['invest_deposit_wallet']  = Transaction::where('wallet_type', 'deposit_wallet')->where('remark', 'invest')->sum('amount');
        $widget['invest_interest_wallet'] = Transaction::where('wallet_type', 'interest_wallet')->where('remark', 'invest')->sum('amount');

        $widget['profit_to_give'] = Invest::where('status', 1)->where('period', '>', 0)->sum('should_pay');
        $widget['profit_paid']    = Invest::where('status', 1)->where('period', '>', 0)->sum('paid');

        $table_prefix = $wpdb->base_prefix;
        
        $interests = Invest::selectRaw("select SUM(paid) as amount, plan_id from `" . $table_prefix . "hyiplab_invests` where `paid` > '0' group by `plan_id` order by `amount` desc");

        $interestByPlans = [];
        foreach($interests as $interest){
            $plan = get_hyiplab_plan($interest->plan_id);
            $interestByPlans[$plan->name] = $interest->amount;
        }

        $totalInterest   = Invest::where('paid', '>', 0)->sum('amount');
        $recentInvests   = Invest::orderBy('id', 'desc')->limit(3)->get();
        $firstInvestYear = Invest::selectRaw("select DATE_FORMAT(created_at, '%Y') as date from `" . $table_prefix . "hyiplab_invests` LIMIT 0,1");
        $firstInvestYear = current($firstInvestYear);

        $this->view('admin/investment/statistics', compact('pageTitle', 'widget', 'interestByPlans', 'totalInterest', 'recentInvests', 'firstInvestYear'));
    }

}
