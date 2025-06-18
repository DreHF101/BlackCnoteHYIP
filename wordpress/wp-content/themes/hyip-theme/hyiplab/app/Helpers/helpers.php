<?php

use Hyiplab\BackOffice\Abort;
use Hyiplab\BackOffice\Facade\DB;
use Hyiplab\Lib\FileManager;
use Hyiplab\BackOffice\Request;
use Hyiplab\BackOffice\Session;
use Hyiplab\BackOffice\System;
use Hyiplab\Lib\Captcha;
use Hyiplab\Lib\ClientInfo;
use Hyiplab\Lib\Initials;
use Hyiplab\Lib\ViserDate;
use Hyiplab\Models\Deposit;
use Hyiplab\Models\Form;
use Hyiplab\Models\Gateway;
use Hyiplab\Models\GatewayCurrency;
use Hyiplab\Models\Invest;
use Hyiplab\Models\Plan;
use Hyiplab\Models\SupportAttachment;
use Hyiplab\Models\SupportTicket;
use Hyiplab\Models\TimeSetting;
use Hyiplab\Models\Transaction;
use Hyiplab\Models\User;
use Hyiplab\Models\Withdrawal;
use Hyiplab\Models\WithdrawMethod;
use Hyiplab\Notify\Notify;

if (!function_exists('hyiplab_system_details')) {
    function hyiplab_system_details()
    {
        $system['prefix'] = 'wp_';
        $system['real_name'] = 'hyiplab';
        $system['name'] = $system['prefix'] . 'hyiplab';
        $system['version'] = '3.0';
        $system['build_version'] = '1.1.3';
        return $system;
    }
}

if (!function_exists('hyiplab_system_instance')) {
    function hyiplab_system_instance()
    {
        return System::getInstance();
    }
}

if (!function_exists('dd')) {
    function dd(...$data)
    {
        foreach ($data as $item) {
            echo "<pre style='background: #001140;color: #00ff4e;padding: 20px;'>";
            print_r($item);
            echo "</pre>";
        }
        exit;
    }
}

if (!function_exists('dump')) {
    function dump(...$data)
    {
        foreach ($data as $item) {
            echo "<pre style='background: #001140;color: #00ff4e;padding: 20px;'>";
            print_r($item);
            echo "</pre>";
        }
    }
}

if (!function_exists('hyiplab_layout')) {
    function hyiplab_layout($hyiplab_layout)
    {
        global $systemLayout;
        $systemLayout = $hyiplab_layout;
    }
}

if (!function_exists('hyiplab_route')) {
    function hyiplab_route($routeName)
    {
        $route = hyiplab_system_instance()->route($routeName);
        return hyiplab_to_object($route);
    }
}

if (!function_exists('hyiplab_to_object')) {
    function hyiplab_to_object($args)
    {
        if (is_array($args)) {
            return (object) array_map(__FUNCTION__, $args);
        } else {
            return $args;
        }
    }
}

if (!function_exists('hyiplab_to_array')) {
    function hyiplab_to_array($args)
    {
        if (is_object($args)) {
            $args = get_object_vars($args);
        }

        if (is_array($args)) {
            return array_map(__FUNCTION__, $args);
        } else {
            return $args;
        }
    }
}


if (!function_exists('hyiplab_redirect')) {
    function hyiplab_redirect($url, $notify = null)
    {
        if ($notify) {
            hyiplab_set_notify($notify);
        }
        wp_redirect($url);
        exit;
    }
}

if (!function_exists('hyiplab_key_to_title')) {
    function hyiplab_key_to_title($text)
    {
        return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
    }
}

if (!function_exists('hyiplab_title_to_key')) {
    function hyiplab_title_to_key($text)
    {
        return strtolower(str_replace(' ', '_', $text));
    }
}

if (!function_exists('hyiplab_request')) {
    function hyiplab_request()
    {
        return new Request();
    }
}

if (!function_exists('hyiplab_remove_session')) {
    function hyiplab_remove_session($key)
    {
        unset($_SESSION[$key]);
    }
}

if (!function_exists('hyiplab_session')) {
    function hyiplab_session()
    {
        return new Session();
    }
}

if (!function_exists('hyiplab_back')) {
    function hyiplab_back($notify = [])
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
        } else {
            $url = home_url();
        }
        hyiplab_redirect($url, $notify);
    }
}

if (!function_exists('hyiplab_old')) {
    function hyiplab_old($key)
    {
        return @hyiplab_session()->get('old_input_value_' . $key);
    }
}

if (!function_exists('hyiplab_abort')) {
    function hyiplab_abort($code = 404, $message = null)
    {
        $abort = new Abort($code, $message);
        $abort->abort();
    }
}

if (!function_exists('hyiplab_query_to_url')) {
    function hyiplab_query_to_url($arr)
    {
        return esc_url(add_query_arg($arr, $_SERVER['REQUEST_URI']));
    }
}

if (!function_exists('hyiplab_set_notify')) {
    function hyiplab_set_notify($data)
    {
        hyiplab_session()->flash('notify', $data);
    }
}

if (!function_exists('hyiplab_include')) {
    function hyiplab_include($view, $data = [])
    {
        extract($data);
        include HYIPLAB_ROOT . 'views/' . $view . '.php';
    }
}

if (!function_exists('hyiplab_ip_info')) {
    function hyiplab_ip_info()
    {
        $ipInfo = ClientInfo::ipInfo();
        return $ipInfo;
    }
}

if (!function_exists('hyiplab_real_ip')) {
    function hyiplab_real_ip()
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        //Deep detect ip
        if (isset($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        }
        if (isset($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if ($ip == '::1') {
            $ip = '127.0.0.1';
        }
        return $ip;
    }
}

if (!function_exists('hyiplab_route_link')) {
    function hyiplab_route_link($name, $format = true)
    {
        $route = hyiplab_to_array(hyiplab_route($name));
        if (array_key_exists('query_string', $route)) {
            $link = menu_page_url('hyiplab', false) . '&module=' . $route['query_string'];
        } else {
            $link = home_url($route['uri']);
        }
        if ($format) {
            return esc_url($link);
        }
        return $link;
    }
}

if (!function_exists('hyiplab_menu_active')) {
    function hyiplab_menu_active($routeName, $type = null, $param = null, $dashboard = false)
    {
        if ($type == 3) $class = 'side-menu--open';
        elseif ($type == 2) $class = 'sidebar-submenu__open';
        else $class = 'active';
        if (!is_array($routeName)) {
            $routeName = [$routeName];
        }
        foreach ($routeName as $key => $value) {
            $route = hyiplab_route($value);
            $queryString = $route->query_string ?? '';
            $uri = $route->uri ?? '';
            if ($queryString) {
                if (isset(hyiplab_request()->module) && hyiplab_request()->module == $queryString) {
                    echo sanitize_html_class($class);
                }
                if (isset(hyiplab_request()->page) && hyiplab_request()->page == HYIPLAB_PLUGIN_NAME && !isset(hyiplab_request()->module) && $dashboard) {
                    echo sanitize_html_class($class);
                }
            } else {
                $currentUri = get_query_var('hyiplab_page');
                if ($currentUri == $uri) {
                    echo sanitize_html_class($class);
                }
            }
        }
    }
}


if (!function_exists('vlIsCurrentRoute')) {
    function vlIsCurrentRoute($routeName)
    {
        $route = hyiplab_route($routeName);
        $queryString = $route->query_string ?? '';
        $uri = $route->uri ?? '';
        if ($queryString) {
            if (isset(hyiplab_request()->module) && hyiplab_request()->module == $queryString) {
                return true;
            }
        } else {
            $currentUri = get_query_var('hyiplab_page');
            if ($currentUri == $uri) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('hyiplab_nonce_field')) {
    function hyiplab_nonce_field($routeName, $isPrint = true)
    {
        $nonce = hyiplab_nonce($routeName);
        if ($isPrint) {
            echo '<input type="hidden" name="nonce" value="' . $nonce . '">';
        } else {
            return '<input type="hidden" name="nonce" value="' . $nonce . '">';
        }
    }
}

if (!function_exists('hyiplab_nonce')) {
    function hyiplab_nonce($routeName)
    {
        $route = hyiplab_to_array(hyiplab_route($routeName));
        if (array_key_exists('query_string', $route)) {
            $nonceName = $route['query_string'];
        } else {
            $nonceName = $route['uri'];
        }
        return wp_create_nonce($nonceName);
    }
}

if (!function_exists('hyiplab_current_route')) {
    function hyiplab_current_route()
    {
        if (isset(hyiplab_request()->page)) {
            if (isset(hyiplab_request()->module)) {
                return hyiplab_request()->module;
            } else {
                return hyiplab_request()->page;
            }
        } else {
            return home_url(get_query_var('hyiplab_page'));
        }
    }
}

if (!function_exists('hyiplab_assets')) {
    function hyiplab_assets($path)
    {
        $path = HYIPLAB_PLUGIN_NAME . '/assets/' . $path;
        $path = str_replace('//', '/', $path);
        return plugins_url($path);
    }
}

if (!function_exists('hyiplab_get_image')) {
    function hyiplab_get_image($image)
    {
        $checkPath = str_replace(plugin_dir_url(dirname(dirname(__FILE__))), plugin_dir_path(dirname(dirname(__FILE__))), $image);
        if (file_exists($checkPath) && is_file($checkPath)) {
            return $image;
        }
        return hyiplab_assets('images/default.png');
    }
}

if (!function_exists('hyiplab_file_uploader')) {
    function hyiplab_file_uploader($file, $location, $size = null, $old = null, $thumb = null)
    {
        $fileManager = new FileManager($file);
        $fileManager->path = $location;
        $fileManager->size = $size;
        $fileManager->old = $old;
        $fileManager->thumb = $thumb;
        $fileManager->upload();
        return $fileManager->filename;
    }
}

if (!function_exists('hyiplab_file_manager')) {
    function hyiplab_file_manager()
    {
        return new FileManager();
    }
}

if (!function_exists('hyiplab_file_path')) {
    function hyiplab_file_path($key)
    {
        $dir = plugin_dir_url(dirname(dirname(__FILE__)));
        if (!empty($_FILES) || !empty($_POST)) {
            $dir = plugin_dir_path(dirname(dirname(__FILE__)));
        }
        return $dir . 'assets/' . hyiplab_file_manager()->$key()->path;
    }
}

if (!function_exists('hyiplab_file_size')) {
    function hyiplab_file_size($key)
    {
        return hyiplab_file_manager()->$key()->size;
    }
}

if (!function_exists('vlGetFileExt')) {
    function vlGetFileExt($key)
    {
        return hyiplab_file_manager()->$key()->extensions;
    }
}

if (!function_exists('hyiplab_push_breadcrumb')) {
    function hyiplab_push_breadcrumb($html)
    {
        add_action('hyiplab_breadcrumb_plugins', function () use ($html) {
            echo  $html;
        });
    }
}

if (!function_exists('hyiplab_check_empty')) {
    function hyiplab_check_empty($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        return empty($data);
    }
}

if (!function_exists('hyiplab_gateway_currency_count')) {
    function hyiplab_gateway_currency_count($code)
    {
        $result = GatewayCurrency::where('method_code', $code)->count();
        return $result;
    }
}

if (!function_exists('hyiplab_title_to_key')) {
    function hyiplab_title_to_key($text)
    {
        return strtolower(str_replace(' ', '_', $text));
    }
}

if (!function_exists('hyiplab_allowed_html')) {
    function hyiplab_allowed_html()
    {
        $arr = array(
            'span' => array(
                'class' => []
            ),
            'br' => [],
            'a' => array(
                'href' => true,
                'class' => [],
            ),
            'em' => array(),
            'b' => array(),
            'bold' => array(),
            'blockquote' => array(),
            'p' => array(),
            'li' => array(
                'class' => [],
                'id' => []
            ),
            'ol' => array(),
            'strong' => array(),
            'ul' => array(
                'id' => [],
                'class' => [], 1
            ),
            'div' => array(
                'id' => [],
                'class' => [], 1
            ),
            'img' => array(
                'src' => true
            ),
            'table' => [],
            'tr' => [],
            'td' => [],
            'i' => array(
                'class' => []
            )
        );
        return $arr;
    }
}

if (!function_exists('hyiplab_currency')) {
    function hyiplab_currency($type = 'text')
    {
        return get_option("hyiplab_cur_$type");
    }
}

if (!function_exists('hyiplab_get_amount')) {
    function hyiplab_get_amount($amount, $length = 2)
    {
        $amount = round($amount, $length);
        return $amount + 0;
    }
}

if (!function_exists('hyiplab_show_amount')) {
    function hyiplab_show_amount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
    {
        $separator = '';
        if ($separate) {
            $separator = ',';
        }
        $printAmount = number_format($amount, $decimal, '.', $separator);
        if ($exceptZeros) {
            $exp = explode('.', $printAmount);
            if ($exp[1] * 1 == 0) {
                $printAmount = $exp[0];
            } else {
                $printAmount = rtrim($printAmount, '0');
            }
        }
        return $printAmount;
    }
}

if (!function_exists('hyiplab_global_notify_short_codes')) {
    function hyiplab_global_notify_short_codes()
    {
        $data['site_name'] = 'Name of your site';
        $data['site_currency'] = 'Currency of your site';
        $data['currency_symbol'] = 'Symbol of currency';
        return $data;
    }
}

if (!function_exists('hyiplab_gateway')) {
    function hyiplab_gateway($code)
    {
        $result = Gateway::where('code', $code)->first();
        return $result;
    }
}

if (!function_exists('hyiplab_withdraw_methods')) {
    function hyiplab_withdraw_methods($id)
    {
        $result = WithdrawMethod::find($id);
        return $result;
    }
}

if (!function_exists('hyiplab_show_date_time')) {
    function hyiplab_show_date_time($date, $format = 'Y-m-d h:i A')
    {
        return hyiplab_date()->parse($date)->toDateTime($format);
    }
}

if (!function_exists('hyiplab_diff_for_humans')) {
    function hyiplab_diff_for_humans($date, $to = '')
    {
        if (empty($to)) {
            $to = current_time('timestamp');
        }
        $from = strtotime($date);
        return human_time_diff($from, $to) . " ago";
    }
}

if (!function_exists('hyiplab_notify')) {
    function hyiplab_notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
    {
        $globalShortCodes = [
            'site_name' => get_bloginfo('name'),
            'site_currency' => hyiplab_currency('text'),
            'currency_symbol' => hyiplab_currency('sym'),
        ];

        if (gettype($user) == 'array') {
            $user = (object) $user;
        }

        $userInfo = [
            'email' => $user->user_email,
            'fullname' => $user->display_name,
            'username' => $user->user_login
        ];

        $userInfo = hyiplab_to_object($userInfo);

        $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);
        $notify = new Notify($sendVia);
        $notify->templateName = $templateName;
        $notify->shortCodes = $shortCodes;
        $notify->user = $userInfo;
        $notify->createLog = $createLog;
        $notify->send();
    }
}

if (!function_exists('hyiplab_auth')) {
    function hyiplab_auth()
    {
        include_once(ABSPATH . 'wp-includes/pluggable.php');
        if (is_user_logged_in()) {
            return (object)[
                'user' => wp_get_current_user(),
                'meta' => get_user_meta(wp_get_current_user()->ID)
            ];
        }
        return false;
    }
}

if (!function_exists('hyiplab_trx')) {
    function hyiplab_trx($length = 12)
    {
        $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('hyiplab_asset')) {
    function hyiplab_asset($path)
    {
        $path = HYIPLAB_PLUGIN_NAME . '/assets/' . $path;
        $path = str_replace('//', '/', $path);
        return plugins_url($path);
    }
}



if (!function_exists('hyiplab_get_form')) {
    function hyiplab_get_form($formId)
    {
        $form = Form::find($formId);
        $formData = [];
        if ($form) {
            $formData = maybe_unserialize($form->form_data);
        }
        extract($formData);
        include HYIPLAB_ROOT . 'views/form/form.php';
    }
}


if (!function_exists('vlKeyToTitle')) {
    function vlKeyToTitle($text)
    {
        return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
    }
}

if (!function_exists('hyiplab_title_to_key')) {
    function hyiplab_title_to_key($text)
    {
        return strtolower(str_replace(' ', '_', $text));
    }
}

if (!function_exists('hyiplab_encrypt')) {
    function hyiplab_encrypt($string)
    {
        return base64_encode($string);
    }
}

if (!function_exists('hyiplab_decrypt')) {
    function hyiplab_decrypt($string)
    {
        return base64_decode($string);
    }
}

if (!function_exists('hyiplab_crypto_qr')) {
    function hyiplab_crypto_qr($wallet)
    {
        return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
    }
}

if (!function_exists('hyiplab_support_ticket')) {
    function hyiplab_support_ticket($id)
    {
        $ticket = SupportTicket::find($id);
        return $ticket;
    }
}

if (!function_exists('hyiplab_support_ticket_attachments')) {
    function hyiplab_support_ticket_attachments($id)
    {
        $attachments = SupportAttachment::where('support_message_id', $id)->get();
        return $attachments;
    }
}

if (!function_exists('hyiplab_gateway_base_symbol')) {
    function hyiplab_gateway_base_symbol($gatewayCurrency, $gateway)
    {
        return $gateway->crypto == 1 ? '$' : $gatewayCurrency->symbol;
    }
}

if (!function_exists('hyiplab_paginate')) {
    function hyiplab_paginate($num = 20)
    {
        return intval($num);
    }
}

if (!function_exists('pending_deposit_count')) {
    function pending_deposit_count()
    {
        $result = Deposit::where('status', 2)->count();
        return intval($result);
    }
}

if (!function_exists('pending_withdraw_count')) {
    function pending_withdraw_count()
    {
        $result = Withdrawal::where('status', 2)->count();
        return intval($result);
    }
}

if (!function_exists('pending_ticket_count')) {
    function pending_ticket_count()
    {
        $result = SupportTicket::whereIn('status', [0, 2])->count();
        return intval($result);
    }
}

if (!function_exists('hyiplab_date')) {
    function hyiplab_date()
    {
        return new ViserDate();
    }
}

function hyiplab_re_captcha()
{
    return Captcha::reCaptcha();
}

function hyiplab_custom_captcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function hyiplab_verify_captcha()
{
    return Captcha::verify();
}

if (!function_exists('getViserInitials')) {
    function getViserInitials($name)
    {
        return Initials::generate($name);
    }
}

if (!function_exists('get_hyiplab_plan')) {
    function get_hyiplab_plan($planId)
    {
        $plan = Plan::where('id', $planId)->first();
        return $plan;
    }
}

if (!function_exists('diffDatePercent')) {
    function diffDatePercent($start, $end)
    {
        $start = strtotime($start);
        $end = strtotime($end);
        $diff = $end - $start;
        $current = current_time('timestamp');
        $cdiff = $current - $start;

        if ($cdiff > $diff) {
            $percentage = 1.0;
        } else if ($current < $start) {
            $percentage = 0.0;
        } else {
            $percentage = $cdiff / $diff;
        }
        return round($percentage * 100, 2);
    }
}

if (!function_exists('hyiplab_balance')) {
    function hyiplab_balance($userId, $type)
    {
        $balance = get_user_meta($userId, 'hyiplab_' . $type, true);
        if ($balance > 0) {
            return hyiplab_get_amount($balance);
        } else {
            return 0;
        }
    }
}

if (!function_exists('getViserReferrer')) {
    function getViserReferrer($userId)
    {
        $refId = get_user_meta($userId, 'hyiplab_ref', true);
        if ($refId) {
            return intval($refId);
        }
        return 0;
    }
}

if (!function_exists('ordinal')) {
    function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }
}

if (!function_exists('getViserAllReferrer')) {
    function getViserAllReferrer($userId, $count = false)
    {
        global $wpdb;
        $table_prefix = $wpdb->base_prefix;
        $data = User::selectRaw("SELECT user_id as id FROM " . $table_prefix . "usermeta WHERE `meta_key` = 'hyiplab_ref' AND `meta_value` = " . $userId);
        if($count === true){
            return count($data);
        }
        return $data;
    }
}

if (!function_exists('hyiplabPlansShortCode')) {
    function hyiplabPlansShortCode($args)
    {
        $attributes = shortcode_atts(array(
            'limit' => 6,
            'ids' => ''
        ), $args);

        $ids = explode(',', $attributes['ids']);
        $ids = array_filter($ids);

        if (empty($ids)) {
            $plans = Plan::where('status', 1)->limit($attributes['limit'])->get();
        } else {
            $plans = Plan::whereIn('id', $ids)->where('status', 1)->get();
        }

        ob_start();
        echo '<div class="row gy-4">';
        hyiplab_include('user/partials/plans', ['plans' => $plans]);
        echo '</div>';
        return ob_get_clean();
    }
}

if (!function_exists('hyiplab_db_prefix')) {
    function hyiplab_db_prefix()
    {
        return DB::tablePrefix();
    }
}

if (!function_exists('hyiplab_db_wpdb')) {
    function hyiplab_db_wpdb()
    {
        return DB::wpdb();
    }
}

if (!function_exists('get_hyiplab_time_setting')) {
    function get_hyiplab_time_setting($id)
    {
        $result = TimeSetting::find($id);
        return $result;
    }
}

if (!function_exists('hyiplab_ajax_invest_statistics')) {
    function hyiplab_ajax_invest_statistics()
    {
        global $wpdb;

        if (!current_user_can('manage_options')) {
            exit();
        }

        $request = new Request();
        $now = current_time('timestamp');

        if ($request->time == 'year') {
            $time = date('Y-01-01', $now);
            $prevTime = hyiplab_date()->parse($time)->subYears(1)->toDate();
        } elseif ($request->time == 'month') {
            $time = date('Y-m-01', $now);
            $prevTime = hyiplab_date()->parse($time)->subMonths(1)->toDate();
        } else {
            $time = date('Y-m-d', hyiplab_date()->subDays(7)->toTimeStamp());
            $prevTime = hyiplab_date()->parse($time)->subMonths(1)->toDate();
        }

        $table_prefix = $wpdb->base_prefix;
        $investments = Invest::selectRaw("select SUM(amount) as amount, DATE_FORMAT(created_at, '%Y-%m-%d') as date from `" . $table_prefix . "hyiplab_invests` where `created_at` >= '" . $time . " ' group by `date`");

        $invests = [];
        foreach ($investments as $invest) {
            $invests[$invest->date] = hyiplab_show_amount($invest->amount);
        }

        $totalInvest = Invest::where('created_at', '>=', $time)->sum('amount');

        $prevInvest = Invest::where('created_at', '>=', $prevTime)->where('created_at', '<', $time)->sum('amount');
        $investDiff = ($prevInvest ? $totalInvest / $prevInvest * 100 - 100 : 0);
        if ($investDiff > 0) {
            $upDown = 'up';
        } else {
            $upDown = 'down';
        }
        $investDiff = abs($investDiff);

        wp_send_json([
            'invests'      => $invests,
            'total_invest' => $totalInvest,
            'invest_diff'  => round($investDiff, 2),
            'up_down'      => $upDown,
        ]);
    }
}
add_action('wp_ajax_invest-total', 'hyiplab_ajax_invest_statistics');

if (!function_exists('hyiplab_ajax_invest_statistics_plan')) {
    function hyiplab_ajax_invest_statistics_plan()
    {
        global $wpdb;

        if (!current_user_can('manage_options')) {
            exit();
        }

        $request = new Request();
        $now = current_time('timestamp');

        if ($request->time == 'year') {
            $time = date('Y-01-01', $now);
        } elseif ($request->time == 'month') {
            $time = date('Y-m-01', $now);
        } elseif ($request->time == 'week') {
            $time = date('Y-m-d', hyiplab_date()->subDays(7)->toTimeStamp());
        } else {
            $time = hyiplab_date()->parse('0000-00-00 00:00:00')->toDate();
        }

        $table_prefix = $wpdb->base_prefix;

        if ($request->invest_type == 'active') {

            $investChart = Invest::selectRaw("select SUM(amount) as investAmount, plan_id from `" . $table_prefix . "hyiplab_invests` where `created_at` >= '" . $time . "' and `status` = '1' group by `plan_id` order by `investAmount` desc");
            $totalInvest = Invest::where('created_at', '>=', $time)->where('status', 1)->sum('amount');
        } elseif ($request->invest_type == 'closed') {
            $investChart = Invest::selectRaw("select SUM(amount) as investAmount, plan_id from `" . $table_prefix . "hyiplab_invests` where `created_at` >= '" . $time . "' and `status` = '0' group by `plan_id` order by `investAmount` desc");
            $totalInvest = Invest::where('created_at', '>=', $time)->where('status', 0)->sum('amount');
        } else {
            $investChart = Invest::selectRaw("select SUM(amount) as investAmount, plan_id from `" . $table_prefix . "hyiplab_invests` where `created_at` >= '" . $time . "' group by `plan_id` order by `investAmount` desc");
            $totalInvest = Invest::where('created_at', '>=', $time)->sum('amount');
        }

        foreach ($investChart as $key => $invest) {
            $plan = get_hyiplab_plan($invest->plan_id);
            $investChart[$key]->plan = esc_html($plan->name);
        }

        wp_send_json([
            'invest_data'  => $investChart,
            'total_invest' => $totalInvest,
        ]);
    }
}
add_action('wp_ajax_invest-plan', 'hyiplab_ajax_invest_statistics_plan');

if (!function_exists('hyiplab_ajax_invest_statistics_interest')) {
    function hyiplab_ajax_invest_statistics_interest()
    {
        if (!current_user_can('manage_options')) {
            exit();
        }

        $request = new Request();
        $now = current_time('timestamp');

        if ($request->time == 'year') {
            $time = date('Y-01-01', $now);
        } elseif ($request->time == 'month') {
            $time = date('Y-m-01', $now);
        } elseif ($request->time == 'week') {
            $time = date('Y-m-d', hyiplab_date()->subDays(7)->toTimeStamp());
        } else {
            $time = hyiplab_date()->parse('0000-00-00 00:00:00')->toDate();
        }

        $runningInvests = Invest::where('status', 1)->where('created_at', '>=', $time)->sum('amount');
        $expiredInvests = Invest::where('status', 0)->where('created_at', '>=', $time)->sum('amount');
        $interests      = Transaction::where('remark', 'interest')->where('created_at', '>=', $time)->sum('amount');

        wp_send_json([
            'running_invests' => hyiplab_show_amount($runningInvests),
            'expired_invests' => hyiplab_show_amount($expiredInvests),
            'interests'       => hyiplab_show_amount($interests),
        ]);
    }
}
add_action('wp_ajax_invest-interest', 'hyiplab_ajax_invest_statistics_interest');

if (!function_exists('hyiplab_ajax_invest_statistics_chart')) {
    function hyiplab_ajax_invest_statistics_chart()
    {
        global $wpdb;
        $request = new Request;
        $table_prefix = $wpdb->base_prefix;

        if (!current_user_can('manage_options')) {
            exit();
        }

        $invests = Invest::selectRaw("select SUM(amount) as amount, DATE_FORMAT(created_at, '%d') as date FROM `" . $table_prefix . "hyiplab_invests` WHERE YEAR(`created_at`) = '" . $request->year . "' AND MONTH(`created_at`) = '" . $request->month . "' group by `date`");
        $investsDate = wp_list_pluck($invests, 'date');

        $interests = Transaction::selectRaw("select SUM(amount) as amount, DATE_FORMAT(created_at, '%d') as date from `" . $table_prefix . "hyiplab_transactions` where year(`created_at`) = '" . $request->year . "' and month(`created_at`) = '" . $request->month . "' and `remark` = 'interest' group by `date`");
        $interestsDate = wp_list_pluck($interests, 'date');

        $dataDates     = array_values(array_unique(array_merge($investsDate, $interestsDate)));
        sort($dataDates);

        $investsData   = [];
        $interestsData = [];

        foreach ($dataDates as $key => $date) {
            $investsData[] = $invests[$key]->amount ?? 0;
            $interestsData[] = $interests[$key]->amount ?? 0;
        }
        wp_send_json([
            'keys'      => $dataDates,
            'invests'   => $investsData,
            'interests' => $interestsData,
        ]);
    }
}
add_action('wp_ajax_invest-chart', 'hyiplab_ajax_invest_statistics_chart');

if (!function_exists('hyiplab_status_badge')) {
    function hyiplab_status_badge($model)
    {
        if ($model->status == 1) {
            echo '<span class="text--small badge badge--success">' . esc_html__("Enabled", HYIPLAB_PLUGIN_NAME) . '</span>';
        } else {
            echo '<span class="text--small badge badge--warning">' . esc_html__("Disabled", HYIPLAB_PLUGIN_NAME) . '</span>';
        }
    }
}

if (!function_exists('pending_kyc_count')) {
    function pending_kyc_count()
    {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
            'hyiplab_kyc',
            '2'
        );
        $user_ids = $wpdb->get_col($query);
        if(empty($user_ids)){
            $result = 0;
        }else{
            $result = User::whereIn('ID', $user_ids)->count();
        }
        return intval($result);
    }
}
if (!function_exists('unverified_kyc_count')) {
    function unverified_kyc_count()
    {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value != %s",
            'hyiplab_kyc',
            '1'
        );
        $user_ids = $wpdb->get_col($query);
        if(empty($user_ids)){
            $result = 0;
        }else{
            $result = User::whereIn('ID', $user_ids)->count();
        }
        return intval($result);
    }
}
