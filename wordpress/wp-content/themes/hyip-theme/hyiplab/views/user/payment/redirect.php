<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php bloginfo('name') ?></title>
</head>

<body>
    <form action="<?php echo esc_url($data->url) ?>" method="<?php echo esc_attr($data->method) ?>" id="auto_submit">
        <?php foreach ($data->val as $k => $v) { ?>
            <input type="hidden" name="<?php echo esc_attr($k) ?>" value="<?php echo esc_attr($v); ?>" />
        <?php } ?>
    </form>
    <script>
        "use strict";
        document.getElementById("auto_submit").submit();
    </script>
</body>

</html>