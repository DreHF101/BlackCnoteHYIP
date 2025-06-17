<?php

namespace Hyiplab\Controllers\User;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\UserRanking;

class InvestController extends Controller
{
    public function index()
    {
        global $user_ID, $wpdb;

        $this->pageTitle = 'Investment Statistics';

        $invests      = Invest::where('user_id', $user_ID)->orderBy('id', 'desc')->where('status', 1)->limit(5)->get();
        $activePlan   = Invest::where('user_id', $user_ID)->where('status', 1)->count();
        $totalInvest  = Invest::where('user_id', $user_ID)->sum('amount');
        $totalProfit  = Transaction::where('remark', 'interest')->where('user_id', $user_ID)->sum('amount');
        $table_prefix = $wpdb->base_prefix;
        $investChart  = Invest::selectRaw("select `plan_id`, SUM(amount) as investAmount from " . $table_prefix . "hyiplab_invests where `user_id` = " . $user_ID . " group by `plan_id` order by `investAmount` desc");
        $this->view('user/invest/index',  compact('invests', 'activePlan', 'totalInvest', 'totalProfit', 'investChart'));
    }

    public function log()
    {
        global $user_ID;
        $this->pageTitle = "Investment Logs";
        $invests = Invest::where('user_id', $user_ID)->orderBy('id', 'desc')->where('status', 1)->paginate(hyiplab_paginate());
        $this->view('user/invest/log', compact('invests'));
    }

    public function detail()
    {
        global $user_ID;
        $request         = new Request();
        $this->pageTitle = "Investment Details";
        try {
            $id = hyiplab_decrypt($request->id);
        } catch (\Throwable $th) {
            hyiplab_abort(404);
        }
        $id = intval($id);
        $invest       = Invest::where('user_id', $user_ID)->findOrFail($id);
        $plan         = get_hyiplab_plan($invest->plan_id);
        $user         = get_userdata($invest->user_id);
        $transactions = Transaction::where('invest_id', $invest->id)->orderBy('id', 'desc')->paginate(hyiplab_paginate());
        $this->view('user/invest/details', compact('invest', 'plan', 'user', 'transactions'));
    }

    public function ranking()
    {
        global $user_ID;
        if(!get_option('hyiplab_user_ranking')){
            hyiplab_abort(404);
        }
        $this->pageTitle = "Rankings";
        $userRankingId = get_user_meta($user_ID, 'hyiplab_ranking_id', true) ?? 0;
        $userRankings = UserRanking::where('status', 1)->get();
        $nextRanking = UserRanking::where('status', 1)->where('id', '>', $userRankingId)->first();
        $total_invests = get_user_meta($user_ID, 'hyiplab_total_invest', true);
        if(!$total_invests){
            $total_invests = 0;
        }
        $team_invests = get_user_meta($user_ID, 'hyiplab_team_invest', true);
        if(!$team_invests){
            $team_invests = 0;
        }
        $activeReferrals = getViserAllReferrer($user_ID, true);
        $this->view('user/ranking', compact('userRankings', 'nextRanking', 'total_invests', 'activeReferrals', 'team_invests', 'userRankingId'));
    }
}
