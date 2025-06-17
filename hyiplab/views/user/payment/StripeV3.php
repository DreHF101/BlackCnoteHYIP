<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php esc_html_e('Deposit with Stripe', HYIPLAB_PLUGIN_NAME); ?></title>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <?php
    $publishable_key = $data->StripeJSAcc->publishable_key;
    $sessionId = $data->session->id;
    ?>

    <script>
        "use strict";
        var stripe = Stripe('<?php echo esc_html($publishable_key) ?>');
        stripe.redirectToCheckout({
            sessionId: '<?php echo esc_html($sessionId) ?>'
        });
    </script>
</body>

</html>