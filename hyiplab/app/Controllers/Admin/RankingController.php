<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\UserRanking;

class RankingController extends Controller
{
    public function index()
    {
        $pageTitle = "User Rankings";
        $userRankings = UserRanking::orderBy('level')->get();
        $this->view('admin/ranking/index', compact('pageTitle', 'userRankings'));
    }

    public function store()
    {
        $request = new Request();

        if ($request->id) {
            $rules = 'image|mimes:jpg,jpeg,png';
        } else {
            $rules = 'required|image|mimes:jpg,jpeg,png';
        }

        $request->validate([
            'level' => 'required|integer',
            'name' => 'required',
            'minimum_invest' => 'required|numeric',
            'team_minimum_invest' => 'required|numeric',
            'min_referral' => 'required|integer',
            'bonus' => 'required|numeric',
            'icon' => $rules
        ]);

        if ($request->id) {
            $userRanking = UserRanking::findOrFail($request->id);
            $notify[]    = ['success', 'User ranking updated successfully'];
        } else {
            $userRanking = new UserRanking();
            $notify[]    = ['success', 'User ranking added successfully'];
        }

        if ($request->hasFile('icon')) {
            try {
                $userRanking->icon = hyiplab_file_uploader($request->icon, hyiplab_file_path('userRanking'), hyiplab_file_size('userRanking'), $userRanking->icon);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your icon'];
                return hyiplab_back($notify);
            }
        }

        $userRanking->level               = intval($request->level);
        $userRanking->name                = sanitize_text_field($request->name);
        $userRanking->minimum_invest      = floatval($request->minimum_invest);
        $userRanking->min_referral_invest = floatval($request->team_minimum_invest);
        $userRanking->min_referral        = intval($request->min_referral);
        $userRanking->bonus               = floatval($request->bonus);
        $userRanking->save();

        hyiplab_back($notify);
    }

    public function status()
    {
        $request = new Request();
        $ranking = UserRanking::findOrFail($request->id);
        $ranking->status = $ranking->status ? 0 : 1;
        $ranking->save();
        $notify[] = ['success', 'Ranking status changed successfully'];
        hyiplab_back($notify);
    }
}
