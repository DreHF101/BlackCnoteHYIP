<?php
define("DONOTCACHEPAGE", true);


$invalid_key = '';
$rp_status = '';

if (isset($_GET['action']) && $_GET['action'] == 'rp' && isset($_POST['action']) && $_POST['action'] != 'rp') {
	$user = check_password_reset_key($_GET['key'], $_GET['login']);
	if (is_wp_error($user)) {
		$invalid_key = 'invalid';
	}
}

if (isset($_POST['action']) && $_POST['action'] == 'rp') {
	$user = get_user_by('login', $_POST['user_login']);
	if ($_POST['pass1'] != $_POST['pass2']) {
		$notify[] = ['error', esc_html__('The passwords do not match.', HYIPLAB_PLUGIN_NAME)];
		hyiplab_set_notify($notify);
	} else if (strlen($_POST['pass1']) < 6) {
		$notify[] = ['error', esc_html__('Passwords must be at least 6 characters long.', HYIPLAB_PLUGIN_NAME)];
		hyiplab_set_notify($notify);
	} else {
		reset_password($user, $_POST['pass1']);
		wp_redirect(home_url('/login/?pw=reset'));
		exit;
	}
}

hyiplab_layout('user/layouts/auth');
?>
<?php if (!isset($_GET['action']) || $invalid_key == 'invalid') {
	hyiplab_include('user/auth/reset_password');
} elseif ($_GET['action'] == 'rp') {
	hyiplab_include('user/auth/change_password');
}
?>