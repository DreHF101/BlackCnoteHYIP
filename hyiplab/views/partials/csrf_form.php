<?php
/**
 * CSRF Form Helper
 * 
 * This file provides reusable CSRF form helpers for views
 * to ensure all forms include proper CSRF protection.
 */

/**
 * Generate a hidden CSRF input field
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Generate a CSRF meta tag for AJAX requests
 */
function csrf_meta(): string
{
    return '<meta name="csrf-token" content="' . csrf_token() . '">';
}

/**
 * Generate a complete form with CSRF protection
 */
function csrf_form(string $action = '', string $method = 'POST', array $attributes = []): string
{
    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
    }
    
    return '<form action="' . htmlspecialchars($action) . '" method="' . htmlspecialchars($method) . '"' . $attrString . '>' . csrf_field();
}

/**
 * Close a CSRF form
 */
function csrf_form_close(): string
{
    return '</form>';
}

/**
 * Generate JavaScript for AJAX CSRF headers
 */
function csrf_ajax_script(): string
{
    return '
    <script>
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $("meta[name=csrf-token]").attr("content")
        }
    });
    
    // Alternative for fetch API
    if (typeof fetch !== "undefined") {
        const originalFetch = fetch;
        fetch = function(url, options = {}) {
            options.headers = options.headers || {};
            options.headers["X-CSRF-TOKEN"] = document.querySelector("meta[name=csrf-token]").getAttribute("content");
            return originalFetch(url, options);
        };
    }
    </script>';
}
?>

<!-- Example Usage in Views -->
<!-- 
<?php csrf_meta(); ?>

<form action="/user/invest" method="POST">
    <?php csrf_field(); ?>
    <input type="text" name="amount" placeholder="Investment Amount">
    <button type="submit">Invest</button>
</form>

<!-- Or using the helper functions -->
<?php echo csrf_form('/user/invest', 'POST', ['class' => 'investment-form']); ?>
    <input type="text" name="amount" placeholder="Investment Amount">
    <button type="submit">Invest</button>
<?php echo csrf_form_close(); ?>

<?php csrf_ajax_script(); ?>
--> 