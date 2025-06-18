<?php

namespace Hyiplab\Controllers\User\Auth;

use Hyiplab\BackOffice\Request;
use Hyiplab\Controllers\Controller;
use Hyiplab\Models\Transaction;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        $info       = json_decode(json_encode(hyiplab_ip_info()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(HYIPLAB_ROOT . 'views/partials/country.json'));
        $this->registrationAction();
        $this->pageTitle = "Create an Account";
        $this->view('user/auth/register', compact('countries', 'mobileCode'));
    }

    public function registrationAction()
    {
        if (isset(hyiplab_request()->action) && hyiplab_request()->action == 'resend') {
            $this->emailResend();
        } else {
            $this->registerUser();
        }
    }

    private function emailResend()
    {
        $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
        $request   = new Request();

        if ($http_post) {
            $request->validate([
                'user_email' => 'required'
            ]);

            if (username_exists($request->user_email)) {
                $user = get_user_by('login', sanitize_user($request->user_email));
            } elseif (email_exists($request->user_email)) {
                $user = get_user_by('email', sanitize_email($request->user_email));
            }

            if (empty($user)) {
                $notify[] = ['error', 'Email or Username not found'];
                hyiplab_back($notify);
            }
            $verify_email = get_user_meta($user->ID, 'hyiplab_email_verify', true);
            if (!$verify_email) {
                $notify[] = ['success', 'Account is already activated.'];
                hyiplab_back($notify);
            }

            $verify_link = sprintf('%s?email=verify&login=%s&key=%s', hyiplab_route_link('user.login'), rawurlencode($user->user_login), $verify_email);

            hyiplab_notify($user, 'REGISTER', [
                'verify_link' => esc_url($verify_link)
            ]);

            $notify[] = ['success', 'Please check your email for activation.'];
            hyiplab_back($notify);
        }
    }


    private function registerUser()
    {
        $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
        $request   = new Request();
        if ($http_post) {
            $request->validate([
                'username' => 'required|min:6',
                'email'    => 'required|email',
                'password' => 'required|confirmed|min:6',
                'country'  => 'required',
                'mobile'   => 'required|integer'
            ]);

            if(!hyiplab_verify_captcha()){
                $notify[] = ['error','Invalid captcha provided'];
                hyiplab_back($notify);
            }

            $this->verifyUser();

            $user_login = sanitize_text_field($request->username);
            $user_email = sanitize_email($request->email);
            $pass1      = sanitize_text_field($request->password);

            $sanitized_user_login = sanitize_user($user_login);

            $user_pass = trim($pass1);
            $user_id   = wp_create_user($sanitized_user_login, $user_pass, $user_email);

            if (!$user_id || is_wp_error($user_id)) {
                $notify[] = ['error', 'Couldn&#8217;t register you&hellip; please contact the Admin'];
                hyiplab_back($notify);
            }

            if( isset($request->referral) ){
                $refUser = get_user_by('login', $request->referral);
                if($refUser){
                    update_user_meta($user_id, 'hyiplab_ref', $refUser->ID);
                }
            }

            update_user_meta($user_id, 'hyiplab_mobile', $request->mobile_code . $request->mobile);
            update_user_meta($user_id, 'hyiplab_country', $request->country);
            update_user_meta($user_id, 'hyiplab_country_code', $request->country_code);

            update_user_meta($user_id, 'hyiplab_deposit_wallet', 0);
            update_user_meta($user_id, 'hyiplab_interest_wallet', 0);
            update_user_meta($user_id, 'hyiplab_interest_wallet', 0);

            if(!get_option('hyiplab_kyc')){
                update_user_meta($user_id, 'hyiplab_kyc', 1);
            }else{
                update_user_meta($user_id, 'hyiplab_kyc', 0);
            }

            if ( get_option('hyiplab_registration_bonus') ) {
                
                $amount = get_option('hyiplab_registration_bonus_amount') ?? 0;
                $afterBalance = hyiplab_balance($user_id, 'deposit_wallet') + $amount;
                update_user_meta($user_id, "hyiplab_deposit_wallet", $afterBalance);
    
                $transaction               = new Transaction();
                $transaction->user_id      = $user_id;
                $transaction->amount       = $amount;
                $transaction->charge       = 0;
                $transaction->post_balance = $afterBalance;
                $transaction->trx_type     = '+';
                $transaction->trx          = hyiplab_trx();
                $transaction->wallet_type  = 'deposit_wallet';
                $transaction->remark       = 'registration_bonus';
                $transaction->details      = 'You have got registration bonus';
                $transaction->created_at   = current_time('mysql');
                $transaction->save();
            }

            $verify_email = wp_generate_password(20, false);

            if (get_option('hyiplab_email_verification')) {

                update_user_meta($user_id, 'hyiplab_email_verify', $verify_email);

                $user = get_userdata($user_id);

                $verify_link = sprintf('%s?email=verify&login=%s&key=%s', hyiplab_route_link('user.login'), rawurlencode($sanitized_user_login), $verify_email);

                hyiplab_notify($user, 'REGISTER', [
                    'verify_link' => $verify_link
                ]);

                $redirect_to = home_url('/login/?registration=done');

            } else {

                $redirect_to = hyiplab_route_link('user.home');
                wp_clear_auth_cookie();
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
            }
            
            wp_safe_redirect($redirect_to);
            exit();
        }
    }

    private function verifyUser()
    {

        $request              = new Request();
        $user_login           = sanitize_text_field($request->username);
        $user_email           = sanitize_email($request->email);
        $sanitized_user_login = sanitize_user($user_login);

        if (!validate_username($user_login)) {
            $notify[] = ['error', 'This username is invalid because it uses illegal characters. Please enter a valid username.'];
            hyiplab_back($notify);
        } elseif (username_exists($sanitized_user_login)) {
            $notify[] = ['error', 'This username is already registered. Please choose another one.'];
            hyiplab_back($notify);
        }
        if (email_exists($user_email)) {
            $notify[] = ['error', 'This email is already registered, please choose another one.'];
            hyiplab_back($notify);
        }
    }
}
