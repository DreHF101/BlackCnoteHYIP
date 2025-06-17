<?php

namespace Hyiplab\Controllers\Admin;

use Hyiplab\BackOffice\Request as BackOfficeRequest;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\CurlRequest;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\Withdrawal;

class AdminController extends Controller
{
    public function dashboard()
    {
        global $wpdb;
        $pageTitle = 'Dashboard';

        $deposit['total']    = Deposit::where('status', 1)->sum('amount');
        $deposit['pending']  = Deposit::where('status', 2)->count();
        $deposit['rejected'] = Deposit::where('status', 3)->count();
        $deposit['charge']   = Deposit::where('status', 1)->sum('charge');

        $withdraw['total']    = Withdrawal::where('status', 1)->sum('amount');
        $withdraw['pending']  = Withdrawal::where('status', 2)->count();
        $withdraw['rejected'] = Withdrawal::where('status', 3)->count();
        $withdraw['charge']   = Withdrawal::where('status', 1)->sum('charge');

        $invest['total']    = Invest::sum('amount');
        $invest['interest'] = Transaction::where('remark', 'interest')->sum('amount');
        $invest['active']   = Invest::where('status', 1)->sum('amount');
        $invest['close']    = Invest::where('status', 0)->sum('amount');

        $table_prefix = $wpdb->base_prefix;
        $date         = hyiplab_date()->subDays(30)->toDate();
        $plusTrx      = Transaction::selectRaw("select SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date from `" . $table_prefix . "hyiplab_transactions` where `trx_type` = '+' and `created_at` >= '" . $date . "' group by `date` order by `created_at` asc");
        $minusTrx     = Transaction::selectRaw("select SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date from `" . $table_prefix . "hyiplab_transactions` where `trx_type` = '-' and `created_at` >= '" . $date . "' group by `date` order by `created_at` asc");
        $this->view('admin/dashboard', compact('pageTitle', 'deposit', 'withdraw', 'plusTrx', 'minusTrx', 'invest'));
    }

    public function requestReport()
    {
        $pageTitle            = 'Your Listed Report & Request';
        $arr['app_name']      = hyiplab_system_details()['name'];
        $arr['app_url']       = home_url();
        $arr['purchase_code'] = get_option(HYIPLAB_PLUGIN_NAME . '_purchase_code', HYIPLAB_PLUGIN_NAME);
        $url                  = "https://license.viserlab.com/issue/get?" . http_build_query($arr);
        $response             = CurlRequest::curlContent($url);
        $response             = json_decode($response);
        if ($response->status == 'error') {
            $notify[] = ['error', 'Something went wrong'];
            hyiplab_redirect(menu_page_url(HYIPLAB_PLUGIN_NAME, false), $notify);
        }
        $reports = $response->message[0];
        $this->view('admin/reports', compact('pageTitle', 'reports'));
    }

    public function requestReportSubmit()
    {
        $request = new BackOfficeRequest;
        $request->validate([
            'type'    => 'required|in:bug,feature',
            'message' => 'required'
        ]);

        $url                  = 'https://license.viserlab.com/issue/add';
        $arr['app_name']      = hyiplab_system_details()['name'];
        $arr['app_url']       = home_url();
        $arr['purchase_code'] = get_option(HYIPLAB_PLUGIN_NAME . '_purchase_code', HYIPLAB_PLUGIN_NAME);
        $arr['req_type']      = $request->type;
        $arr['message']       = $request->message;
        $response             = CurlRequest::curlPostContent($url, $arr);
        $response             = json_decode($response);
        if ($response->status == 'error') {
            $notify[] = ['error', 'Something went wrong'];
            hyiplab_back($notify);
        }
        $notify[] = ['success', $response->message];
        hyiplab_back($notify);
    }

    public function download()
    {
        $file      = hyiplab_request()->file_path;
        $file      = hyiplab_decrypt($file);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $title     = hyiplab_title_to_key(get_bloginfo('name')) . '_attachments.' . $extension;
        $mimetype  = mime_content_type($file);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        ob_clean();
        flush();
        return readfile($file);
    }
}
