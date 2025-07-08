<?php
/**
 * BlackCnote React Integration Simple Test
 * Tests React app integration without WordPress dependencies
 */

echo "=== BlackCnote React Integration Simple Test ===\n\n";

// Test 1: Check React Dev Server
echo "1. Testing React Dev Server:\n";
$react_dev_url = 'http://localhost:5176';

$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'GET'
    ]
]);

$response = @file_get_contents($react_dev_url, false, $context);
if ($response !== false) {
    echo "   ✓ React dev server accessible at {$react_dev_url}\n";
    
    if (strpos($response, 'BlackCnote') !== false) {
        echo "   ✓ React app content found\n";
    } else {
        echo "   ⚠ React app content not found in response\n";
    }
    
    if (strpos($response, 'main.tsx') !== false) {
        echo "   ✓ React main entry point found\n";
    } else {
        echo "   ✗ React main entry point not found\n";
    }
} else {
    echo "   ✗ React dev server not accessible\n";
    $error = error_get_last();
    if ($error) {
        echo "   Error: " . $error['message'] . "\n";
    }
}

// Test 2: Check WordPress Admin
echo "\n2. Testing WordPress Admin:\n";
$wp_admin_url = 'http://localhost:8888/wp-admin/';

$response = @file_get_contents($wp_admin_url, false, $context);
if ($response !== false) {
    echo "   ✓ WordPress admin accessible at {$wp_admin_url}\n";
    
    // Check for React container
    if (strpos($response, 'id="root"') !== false) {
        echo "   ✓ React container found in admin page\n";
    } else {
        echo "   ✗ React container not found in admin page\n";
    }
    
    // Check for React loading message
    if (strpos($response, 'Loading BlackCnote') !== false) {
        echo "   ✓ React loading message found\n";
    } else {
        echo "   ✗ React loading message not found\n";
    }
    
    // Check for React scripts
    if (strpos($response, 'localhost:5176') !== false) {
        echo "   ✓ React dev server scripts found\n";
    } else {
        echo "   ✗ React dev server scripts not found\n";
    }
    
    // Check for API settings
    if (strpos($response, 'blackCnoteApiSettings') !== false) {
        echo "   ✓ React API settings found\n";
    } else {
        echo "   ✗ React API settings not found\n";
    }
    
} else {
    echo "   ✗ WordPress admin not accessible\n";
    $error = error_get_last();
    if ($error) {
        echo "   Error: " . $error['message'] . "\n";
    }
}

// Test 3: Check Theme Files
echo "\n3. Testing Theme Files:\n";
$theme_functions = 'blackcnote/wp-content/themes/blackcnote/functions.php';
if (file_exists($theme_functions)) {
    echo "   ✓ Theme functions file exists\n";
    
    $content = file_get_contents($theme_functions);
    
    if (strpos($content, 'blackcnote_check_react_dev_server') !== false) {
        echo "   ✓ React dev server check function found\n";
    } else {
        echo "   ✗ React dev server check function not found\n";
    }
    
    if (strpos($content, 'localhost:5176') !== false) {
        echo "   ✓ React dev server URL configured\n";
    } else {
        echo "   ✗ React dev server URL not configured\n";
    }
    
    if (strpos($content, 'blackcnote_enqueue_react_app') !== false) {
        echo "   ✓ React app enqueue function found\n";
    } else {
        echo "   ✗ React app enqueue function not found\n";
    }
    
} else {
    echo "   ✗ Theme functions file not found\n";
}

// Test 4: Check React Loader
echo "\n4. Testing React Loader:\n";
$react_loader = 'blackcnote/wp-content/themes/blackcnote/inc/blackcnote-react-loader.php';
if (file_exists($react_loader)) {
    echo "   ✓ React loader file exists\n";
    
    $content = file_get_contents($react_loader);
    
    if (strpos($content, 'blackcnote_add_react_container') !== false) {
        echo "   ✓ React container function found\n";
    } else {
        echo "   ✗ React container function not found\n";
    }
    
    if (strpos($content, 'is_admin()') !== false) {
        echo "   ✓ Admin page handling found\n";
    } else {
        echo "   ✗ Admin page handling not found\n";
    }
    
} else {
    echo "   ✗ React loader file not found\n";
}

// Test 5: Check Docker Services
echo "\n5. Testing Docker Services:\n";
$docker_ps = shell_exec('docker ps --filter "name=blackcnote" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"');
if ($docker_ps) {
    echo "   Docker containers:\n";
    $lines = explode("\n", trim($docker_ps));
    foreach ($lines as $line) {
        if (trim($line) && !strpos($line, 'NAMES')) {
            echo "   " . trim($line) . "\n";
        }
    }
} else {
    echo "   ✗ Docker containers not found\n";
}

// Test 6: Check React App Files
echo "\n6. Testing React App Files:\n";
$react_app_dir = 'react-app';
if (is_dir($react_app_dir)) {
    echo "   ✓ React app directory exists\n";
    
    $main_tsx = $react_app_dir . '/src/main.tsx';
    if (file_exists($main_tsx)) {
        echo "   ✓ React main.tsx file exists\n";
    } else {
        echo "   ✗ React main.tsx file not found\n";
    }
    
    $package_json = $react_app_dir . '/package.json';
    if (file_exists($package_json)) {
        echo "   ✓ React package.json exists\n";
    } else {
        echo "   ✗ React package.json not found\n";
    }
    
} else {
    echo "   ✗ React app directory not found\n";
}

// Test 7: Recommendations
echo "\n7. Recommendations:\n";
echo "   - Check browser console for JavaScript errors\n";
echo "   - Verify React dev server is running on port 5176\n";
echo "   - Check if WordPress is blocking external scripts\n";
echo "   - Verify theme is properly activated\n";
echo "   - Check for plugin conflicts\n";
echo "   - Clear browser cache and reload\n";
echo "   - Check for CORS issues in browser developer tools\n";

echo "\n=== Test Complete ===\n"; 