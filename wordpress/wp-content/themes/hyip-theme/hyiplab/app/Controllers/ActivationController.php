<?php

namespace Hyiplab\Controllers;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Lib\CurlRequest;
use Hyiplab\Lib\VerifiedPlugin;

class ActivationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $current_user = wp_get_current_user();
        if (!user_can( $current_user, 'administrator' )) {
            hyiplab_back();
        }
    }

    public function activate()
    {
        if (VerifiedPlugin::check()) {
            hyiplab_redirect(admin_url('/admin.php?page='.HYIPLAB_PLUGIN_NAME));
        }
        $this->view('activation');
    }

    public function activationSubmit()
    {
        $request = new Request;
        $param['code'] = $request->purchase_code;
        $param['url'] = home_url();
        $param['user'] = $request->envato_username;
        $param['email'] = $request->email;
        $param['product'] = hyiplab_system_details()['name'];
        $url = str_rot13('uggcf://yvprafr.ivfreyno.pbz/npgvingr');
        $response = CurlRequest::curlPostContent($url, $param);
        $response = json_decode($response);

        if ($response->error == 'error') {
            wp_send_json(['type'=>'error','message'=>$response->message]);
            die;
        }

        $viser = fopen(HYIPLAB_ROOT.'/viser.json', "w");
        $txt = '{
            "license_type":'.'"'.$response->license_type.'"'.'
        }';
        fwrite($viser, $txt);
        fclose($viser);

        update_option(HYIPLAB_PLUGIN_NAME . '_purchase_code', $response->installcode);
        update_option(HYIPLAB_PLUGIN_NAME.'_activated',1);
        update_option(HYIPLAB_PLUGIN_NAME.'_maintenance_mode',0);

        wp_send_json(['type'=>'success']);
        die;
    }


}