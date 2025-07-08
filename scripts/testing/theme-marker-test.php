<?php
/**
 * Theme Marker Test for BlackCnote
 * Tests if the BCnote Theme marker is visible in the browser
 */

echo "🔍 BCnote Theme Marker Test\n";
echo "==========================\n\n";

// Test WordPress accessibility and theme marker
$url = 'http://localhost:8888';

echo "Testing WordPress at: $url\n\n";

// Get the page content
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET',
        'user_agent' => 'BlackCnote-Test/1.0'
    ]
]);

$result = @file_get_contents($url, false, $context);

if ($result !== false) {
    echo "✅ WordPress is accessible\n\n";
    
    // Check for BCnote Theme marker
    if (strpos($result, '<!-- BCnote Theme -->') !== false) {
        echo "✅ BCnote Theme marker found in HTML source\n";
        echo "   Location: After DOCTYPE declaration\n\n";
    } else {
        echo "❌ BCnote Theme marker NOT found in HTML source\n\n";
    }
    
    // Check for WordPress content
    if (strpos($result, 'wp-content') !== false) {
        echo "✅ WordPress content detected\n";
    } else {
        echo "⚠️  WordPress content not detected\n";
    }
    
    // Check for theme-specific content
    if (strpos($result, 'blackcnote') !== false) {
        echo "✅ BlackCnote theme content detected\n";
    } else {
        echo "⚠️  BlackCnote theme content not detected\n";
    }
    
    // Check for theme structure
    if (strpos($result, 'site-header') !== false) {
        echo "✅ Theme header structure detected\n";
    } else {
        echo "⚠️  Theme header structure not detected\n";
    }
    
    // Show first 500 characters of HTML
    echo "\n📄 First 500 characters of HTML:\n";
    echo str_repeat("-", 50) . "\n";
    echo htmlspecialchars(substr($result, 0, 500)) . "\n";
    echo str_repeat("-", 50) . "\n";
    
} else {
    echo "❌ WordPress is not accessible\n";
    echo "   Please check if Docker containers are running\n";
    echo "   Run: docker-compose -f config/docker/docker-compose.yml up -d\n";
}

echo "\n🔧 Troubleshooting:\n";
echo "1. If BCnote Theme marker not found, check if theme is active\n";
echo "2. Visit http://localhost:8888/wp-admin > Appearance > Themes\n";
echo "3. Activate BlackCnote theme if not active\n";
echo "4. Clear any caching\n";
echo "5. Check browser developer tools for any errors\n";

echo "\n✅ Theme marker test completed!\n";
?> 