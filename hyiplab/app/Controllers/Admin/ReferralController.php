<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Referral;

class ReferralController extends Controller
{
    public function index()
    {
        $pageTitle       = "Manage Referral";
        $referrals       = Referral::get();
        $commissionTypes = [
            'deposit_commission'       => 'Deposit Commission',
            'invest_commission'        => 'Invest Commission',
            'invest_return_commission' => 'Interest Commission',
        ];
        $this->view('admin/referral/index', compact('pageTitle', 'referrals', 'commissionTypes'));
    }

    public function status()
    {
        $request = new Request();
        if (get_option('hyiplab_' . $request->type)) {
            update_option('hyiplab_' . $request->type, 0);
        } else {
            update_option('hyiplab_' . $request->type, 1);
        }
        $notify[] = ['success', 'Referral commission status updated successfully'];
        hyiplab_back($notify);
    }

    public function update()
    {
        $request = new Request();
        
        $request->validate([
            'percent'         => 'required',
            'commission_type' => 'required|in:deposit_commission,invest_commission,invest_return_commission',
        ]);

        $type = $request->commission_type;

        Referral::where('commission_type', $type)->delete();

        for ($i = 0; $i < count($request->percent); $i++) {
            $referral                  = new Referral();
            $referral->level           = $i + 1;
            $referral->percent         = $request->percent[$i];
            $referral->commission_type = $request->commission_type;
            $referral->save();
        }
        $notify[] = ['success', 'Referral commission setting updated successfully'];
        hyiplab_back($notify);
    }
}
