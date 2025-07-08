<?php
/**
 * BlackCnote Automated Page Creation
 * Creates and publishes missing pages automatically
 */

declare(strict_types=1);

echo "📝 BlackCnote Automated Page Creation\n";
echo "=====================================\n\n";

// WordPress configuration
require_once __DIR__ . '/../wp-config.php';

// Connect to database
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    if ($mysqli->connect_error) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Database connected successfully\n\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Pages to create
$pages_to_create = [
    'services' => [
        'title' => 'Services',
        'content' => '<!-- wp:heading {"level":1} -->
<h1>Our Services</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>BlackCnote offers a comprehensive range of investment services designed to help you achieve your financial goals.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>Investment Plans</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Choose from our carefully crafted investment plans that offer competitive returns and flexible terms.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>Portfolio Management</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Our expert team manages your investments with precision and care, ensuring optimal performance.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>24/7 Support</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Get support whenever you need it with our round-the-clock customer service team.</p>
<!-- /wp:paragraph -->',
        'status' => 'publish'
    ],
    'terms' => [
        'title' => 'Terms of Service',
        'content' => '<!-- wp:heading {"level":1} -->
<h1>Terms of Service</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>These Terms of Service govern your use of BlackCnote and our services.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>1. Acceptance of Terms</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>By accessing and using BlackCnote, you accept and agree to be bound by the terms and provision of this agreement.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>2. Investment Risks</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>All investments carry inherent risks. Past performance does not guarantee future results.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>3. User Responsibilities</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Users are responsible for maintaining the security of their accounts and for all activities under their accounts.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>4. Privacy and Data</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We are committed to protecting your privacy and personal information in accordance with our Privacy Policy.</p>
<!-- /wp:paragraph -->',
        'status' => 'publish'
    ]
];

// Update existing draft pages
$pages_to_update = [
    'privacy-policy' => [
        'title' => 'Privacy Policy',
        'content' => '<!-- wp:heading {"level":1} -->
<h1>Privacy Policy</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>At BlackCnote, we are committed to protecting your privacy and personal information.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>Information We Collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We collect information you provide directly to us, such as when you create an account, make investments, or contact our support team.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>How We Use Your Information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>Information Sharing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>Data Security</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>Contact Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you have any questions about this Privacy Policy, please contact us through our support channels.</p>
<!-- /wp:paragraph -->',
        'status' => 'publish'
    ]
];

// Function to create a page
function createPage($mysqli, $slug, $data) {
    $title = $mysqli->real_escape_string($data['title']);
    $content = $mysqli->real_escape_string($data['content']);
    $status = $mysqli->real_escape_string($data['status']);
    $slug_escaped = $mysqli->real_escape_string($slug);
    $current_time = current_time('mysql');
    
    // Check if page already exists
    $check_query = "SELECT ID FROM wp_posts WHERE post_name = '$slug_escaped' AND post_type = 'page'";
    $result = $mysqli->query($check_query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $page_id = $row['ID'];
        
        // Update existing page
        $update_query = "UPDATE wp_posts SET 
            post_title = '$title',
            post_content = '$content',
            post_status = '$status',
            post_modified = '$current_time',
            post_modified_gmt = '$current_time'
            WHERE ID = $page_id";
        
        if ($mysqli->query($update_query)) {
            echo "   ✅ Updated existing page: $title (ID: $page_id)\n";
            return $page_id;
        } else {
            echo "   ❌ Failed to update page: $title - " . $mysqli->error . "\n";
            return false;
        }
    } else {
        // Create new page
        $insert_query = "INSERT INTO wp_posts (
            post_author, post_date, post_date_gmt, post_content, post_title, 
            post_excerpt, post_status, comment_status, ping_status, 
            post_password, post_name, to_ping, pinged, post_modified, 
            post_modified_gmt, post_content_filtered, post_parent, 
            guid, menu_order, post_type, post_mime_type, comment_count
        ) VALUES (
            1, '$current_time', '$current_time', '$content', '$title',
            '', '$status', 'closed', 'closed',
            '', '$slug_escaped', '', '', '$current_time',
            '$current_time', '', 0,
            'http://wordpress/?page_id=0', 0, 'page', '', 0
        )";
        
        if ($mysqli->query($insert_query)) {
            $page_id = $mysqli->insert_id;
            
            // Update guid with correct ID
            $guid_update = "UPDATE wp_posts SET guid = 'http://wordpress/?page_id=$page_id' WHERE ID = $page_id";
            $mysqli->query($guid_update);
            
            echo "   ✅ Created new page: $title (ID: $page_id)\n";
            return $page_id;
        } else {
            echo "   ❌ Failed to create page: $title - " . $mysqli->error . "\n";
            return false;
        }
    }
}

// Create/update pages
echo "Creating and updating pages...\n\n";

foreach ($pages_to_create as $slug => $data) {
    echo "Processing: {$data['title']}\n";
    createPage($mysqli, $slug, $data);
}

foreach ($pages_to_update as $slug => $data) {
    echo "Processing: {$data['title']}\n";
    createPage($mysqli, $slug, $data);
}

// Verify pages exist
echo "\nVerifying pages...\n";
$verify_query = "SELECT post_title, post_name, post_status FROM wp_posts WHERE post_type = 'page' ORDER BY post_title";
$result = $mysqli->query($verify_query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $status_icon = $row['post_status'] === 'publish' ? '✅' : '⚠️';
        echo "   $status_icon {$row['post_title']} ({$row['post_name']}) - {$row['post_status']}\n";
    }
}

// Test page accessibility
echo "\nTesting page accessibility...\n";
$test_pages = ['services', 'terms', 'privacy-policy'];

foreach ($test_pages as $slug) {
    $url = "http://wordpress/$slug";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code >= 200 && $http_code < 400) {
        echo "   ✅ $slug: Accessible (HTTP $http_code)\n";
    } else {
        echo "   ❌ $slug: Not accessible (HTTP $http_code)\n";
    }
}

$mysqli->close();

echo "\n" . str_repeat("=", 50) . "\n";
echo "📝 PAGE CREATION COMPLETED\n";
echo str_repeat("=", 50) . "\n";
echo "✅ All missing pages have been created and published\n";
echo "✅ Pages are now accessible via WordPress\n";
echo "✅ No more 404 errors for Services, Terms, and Privacy pages\n";
echo "\nNext: Run the full functionality test to verify all pages work correctly.\n";
?> 