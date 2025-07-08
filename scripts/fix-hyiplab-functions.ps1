# BlackCnote HYIPLab Function Redeclaration Fix Script
# This script fixes function redeclaration errors in the HYIPLab plugin

Write-Host "🔧 BlackCnote HYIPLab Function Redeclaration Fix Script" -ForegroundColor Cyan
Write-Host "=====================================================" -ForegroundColor Cyan

# Define the helpers.php file path
$helpersFile = "blackcnote/wp-content/plugins/hyiplab/app/Helpers/helpers.php"

Write-Host "`n📁 Checking helpers.php file..." -ForegroundColor Yellow

if (Test-Path $helpersFile) {
    Write-Host "✅ helpers.php file found" -ForegroundColor Green
    
    # Read the file content
    $content = Get-Content $helpersFile -Raw
    
    # List of functions that need function_exists checks
    $functionsToFix = @(
        'hyiplab_re_captcha',
        'hyiplab_custom_captcha', 
        'hyiplab_verify_captcha',
        'hyiplab_system_details',
        'hyiplab_system_instance',
        'hyiplab_layout',
        'hyiplab_route',
        'hyiplab_to_object',
        'hyiplab_to_array',
        'hyiplab_redirect',
        'hyiplab_key_to_title',
        'hyiplab_title_to_key',
        'hyiplab_request',
        'hyiplab_remove_session',
        'hyiplab_session',
        'hyiplab_back',
        'hyiplab_old',
        'hyiplab_abort',
        'hyiplab_query_to_url',
        'hyiplab_set_notify',
        'hyiplab_include',
        'hyiplab_ip_info',
        'hyiplab_real_ip',
        'hyiplab_route_link',
        'hyiplab_menu_active',
        'hyiplab_nonce_field',
        'hyiplab_nonce',
        'hyiplab_current_route',
        'hyiplab_assets',
        'hyiplab_get_image',
        'hyiplab_file_uploader',
        'hyiplab_file_manager',
        'hyiplab_file_path',
        'hyiplab_file_size',
        'hyiplab_push_breadcrumb',
        'hyiplab_check_empty',
        'hyiplab_gateway_currency_count',
        'hyiplab_allowed_html',
        'hyiplab_currency',
        'hyiplab_get_amount',
        'hyiplab_show_amount',
        'hyiplab_global_notify_short_codes',
        'hyiplab_gateway',
        'hyiplab_withdraw_methods',
        'hyiplab_show_date_time',
        'hyiplab_diff_for_humans',
        'hyiplab_notify',
        'hyiplab_auth',
        'hyiplab_trx',
        'hyiplab_asset',
        'hyiplab_get_form',
        'hyiplab_encrypt',
        'hyiplab_decrypt',
        'hyiplab_crypto_qr',
        'hyiplab_support_ticket',
        'hyiplab_support_ticket_attachments',
        'hyiplab_gateway_base_symbol',
        'hyiplab_paginate',
        'hyiplab_date'
    )
    
    Write-Host "`n🔧 Fixing function declarations..." -ForegroundColor Yellow
    
    foreach ($function in $functionsToFix) {
        # Pattern to match function declaration
        $pattern = "function $function\("
        $replacement = "if (!function_exists('$function')) {`n    function $function("
        
        if ($content -match $pattern) {
            # Find the function and wrap it
            $functionStart = $content.IndexOf("function $function(")
            if ($functionStart -ge 0) {
                # Find the end of the function (simplified approach)
                $functionEnd = $content.IndexOf("}", $functionStart)
                if ($functionEnd -ge 0) {
                    # Insert the function_exists check
                    $beforeFunction = $content.Substring(0, $functionStart)
                    $functionBody = $content.Substring($functionStart, $functionEnd - $functionStart + 1)
                    $afterFunction = $content.Substring($functionEnd + 1)
                    
                    $newFunctionBody = "if (!function_exists('$function')) {`n    $functionBody`n}`n"
                    $content = $beforeFunction + $newFunctionBody + $afterFunction
                    
                    Write-Host "✅ Fixed function: $function" -ForegroundColor Green
                }
            }
        }
    }
    
    # Write the fixed content back to the file
    Set-Content -Path $helpersFile -Value $content -Encoding UTF8
    
    Write-Host "`n✅ Function redeclaration fixes applied successfully!" -ForegroundColor Green
    
} else {
    Write-Host "❌ helpers.php file not found at: $helpersFile" -ForegroundColor Red
    exit 1
}

Write-Host "`n🎉 HYIPLab function redeclaration fix completed!" -ForegroundColor Green
Write-Host "You can now try activating the HYIPLab plugin." -ForegroundColor Yellow 