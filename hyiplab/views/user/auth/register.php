<?php
define("DONOTCACHEPAGE", true);
if (isset($_GET['reference']) && $_GET['reference'] != '') {
	hyiplab_session()->put('reference', $_GET['reference']);
}
hyiplab_layout('user/layouts/auth');
?>
<?php if (isset($_GET['action']) && $_GET['action'] == 'resend') {
	hyiplab_include('user/auth/resend_activation');
} else {
	hyiplab_include('user/auth/register_form', compact('countries', 'mobileCode'));
} ?>