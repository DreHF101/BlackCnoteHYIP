<?php

namespace Hyiplab\Middleware;

class VerifyNonce{

    protected $exceptVerify = [
        
    ];

    public function filterRequest()
    {
        if($this->shouldVerify()){
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nonce = hyiplab_request()->nonce;
                if (!$nonce) {
                    hyiplab_abort(404);
                }
                if (get_query_var('hyiplab_page')) {
                    $currentRoute = get_query_var('hyiplab_page');
                }else{
                    $currentRoute = hyiplab_current_route();
                }
                if (!wp_verify_nonce($nonce,$currentRoute)) {
                    hyiplab_abort(404);
                }
            }
        }
    }

    public function shouldVerify(){
        if(in_array(get_query_var('hyiplab_page'), $this->exceptVerify)) return false;
        return true;
    }
}