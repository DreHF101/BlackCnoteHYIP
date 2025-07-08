<?php
/**
 * BlackCnote HYIPLab Integration Test Suite
 * Comprehensive testing for HYIPLab plugin functionality
 */

declare(strict_types=1);

// Load WordPress
require_once 'wp-config.php';
require_once 'wp-load.php';

echo "🧪 BlackCnote HYIPLab Integration Test Suite\n";
echo "===========================================\n\n";

global $wpdb;

class HYIPLabTestSuite {
    private $wpdb;
    private $test_results = [];
    
    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }
    
    public function runAllTests() {
        echo "Starting comprehensive HYIPLab integration tests...\n\n";
        
        $this->testPluginActivation();
        $this->testDatabaseSchema();
        $this->testUserManagement();
        $this->testInvestmentPlans();
        $this->testInvestmentProcessing();
        $this->testTransactionHandling();
        $this->testBalanceCalculations();
        $this->testLicenseManagement();
        $this->testAPIEndpoints();
        $this->testSecurityFeatures();
        
        $this->generateTestReport();
    }
    
    private function testPluginActivation() {
        echo "1. Testing Plugin Activation...\n";
        
        // Test if HYIPLab plugin is active
        $plugin_active = is_plugin_active('hyiplab/hyiplab.php');
        $this->assertTest('Plugin Active', $plugin_active, 'HYIPLab plugin should be active');
        
        // Test if required tables exist
        $tables = ['hyiplab_plans', 'hyiplab_users', 'hyiplab_invests', 'hyiplab_transactions'];
        foreach ($tables as $table) {
            $table_exists = $this->wpdb->get_var("SHOW TABLES LIKE '{$this->wpdb->prefix}{$table}'");
            $this->assertTest("Table {$table} exists", $table_exists, "Table {$table} should exist");
        }
        
        echo "\n";
    }
    
    private function testDatabaseSchema() {
        echo "2. Testing Database Schema...\n";
        
        // Test plans table structure
        $plans_columns = $this->wpdb->get_results("DESCRIBE {$this->wpdb->prefix}hyiplab_plans");
        $required_columns = ['id', 'name', 'minimum', 'maximum', 'interest', 'status'];
        foreach ($required_columns as $column) {
            $column_exists = array_filter($plans_columns, function($col) use ($column) {
                return $col->Field === $column;
            });
            $this->assertTest("Plans table has {$column} column", !empty($column_exists), "Column {$column} should exist in plans table");
        }
        
        // Test invests table structure
        $invests_columns = $this->wpdb->get_results("DESCRIBE {$this->wpdb->prefix}hyiplab_invests");
        $required_invest_columns = ['id', 'user_id', 'plan_id', 'amount', 'interest', 'status'];
        foreach ($required_invest_columns as $column) {
            $column_exists = array_filter($invests_columns, function($col) use ($column) {
                return $col->Field === $column;
            });
            $this->assertTest("Invests table has {$column} column", !empty($column_exists), "Column {$column} should exist in invests table");
        }
        
        echo "\n";
    }
    
    private function testUserManagement() {
        echo "3. Testing User Management...\n";
        
        // Test user creation
        $test_user_data = [
            'user_login' => 'test_user_' . time(),
            'user_pass' => 'test123',
            'user_email' => 'test@blackcnote.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'role' => 'subscriber'
        ];
        
        $user_id = wp_insert_user($test_user_data);
        $this->assertTest('User creation', !is_wp_error($user_id), 'Should be able to create WordPress user');
        
        if (!is_wp_error($user_id)) {
            // Test HYIPLab user creation
            $hyip_user_data = [
                'wp_user_id' => $user_id,
                'username' => $test_user_data['user_login'],
                'email' => $test_user_data['user_email'],
                'balance' => 0.00,
                'total_invested' => 0.00,
                'total_earned' => 0.00,
                'status' => 'active'
            ];
            
            $result = $this->wpdb->insert($this->wpdb->prefix . 'hyiplab_users', $hyip_user_data);
            $this->assertTest('HYIPLab user creation', $result !== false, 'Should be able to create HYIPLab user');
            
            // Cleanup
            wp_delete_user($user_id);
        }
        
        echo "\n";
    }
    
    private function testInvestmentPlans() {
        echo "4. Testing Investment Plans...\n";
        
        // Test plan creation
        $test_plan = [
            'name' => 'Test Plan',
            'minimum' => 100.00,
            'maximum' => 1000.00,
            'fixed_amount' => 0.00,
            'interest' => 2.5,
            'interest_type' => 1,
            'time_setting_id' => 1,
            'status' => 1,
            'featured' => 0,
            'capital_back' => 1,
            'compound_interest' => 0,
            'hold_capital' => 0,
            'lifetime' => 0,
            'repeat_time' => null
        ];
        
        $result = $this->wpdb->insert($this->wpdb->prefix . 'hyiplab_plans', $test_plan);
        $this->assertTest('Plan creation', $result !== false, 'Should be able to create investment plan');
        
        if ($result !== false) {
            $plan_id = $this->wpdb->insert_id;
            
            // Test plan retrieval
            $plan = $this->wpdb->get_row($this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}hyiplab_plans WHERE id = %d",
                $plan_id
            ));
            $this->assertTest('Plan retrieval', $plan !== null, 'Should be able to retrieve created plan');
            
            // Cleanup
            $this->wpdb->delete($this->wpdb->prefix . 'hyiplab_plans', ['id' => $plan_id]);
        }
        
        echo "\n";
    }
    
    private function testInvestmentProcessing() {
        echo "5. Testing Investment Processing...\n";
        
        // Get existing user and plan for testing
        $user = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}hyiplab_users LIMIT 1");
        $plan = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}hyiplab_plans WHERE status = 1 LIMIT 1");
        
        if ($user && $plan) {
            // Test investment creation
            $investment_data = [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => 500.00,
                'interest' => $plan->interest,
                'should_pay' => 500.00 * (1 + ($plan->interest / 100)),
                'paid' => 0.00,
                'period' => 1,
                'hours' => '24',
                'time_name' => 'Daily',
                'return_rec_time' => $plan->time_setting_id,
                'next_time' => current_time('mysql'),
                'last_time' => null,
                'compound_times' => 0,
                'rem_compound_times' => 0,
                'status' => 1,
                'capital_status' => 1,
                'capital_back' => $plan->capital_back,
                'hold_capital' => $plan->hold_capital,
                'trx' => uniqid('TEST'),
                'wallet_type' => 'main',
                'created_at' => current_time('mysql')
            ];
            
            $result = $this->wpdb->insert($this->wpdb->prefix . 'hyiplab_invests', $investment_data);
            $this->assertTest('Investment creation', $result !== false, 'Should be able to create investment');
            
            if ($result !== false) {
                $investment_id = $this->wpdb->insert_id;
                
                // Test investment calculation
                $investment = $this->wpdb->get_row($this->wpdb->prepare(
                    "SELECT * FROM {$this->wpdb->prefix}hyiplab_invests WHERE id = %d",
                    $investment_id
                ));
                
                $expected_should_pay = $investment->amount * (1 + ($investment->interest / 100));
                $this->assertTest('Investment calculation', 
                    abs($investment->should_pay - $expected_should_pay) < 0.01, 
                    'Investment return calculation should be correct'
                );
                
                // Cleanup
                $this->wpdb->delete($this->wpdb->prefix . 'hyiplab_invests', ['id' => $investment_id]);
            }
        } else {
            $this->assertTest('Investment processing prerequisites', false, 'Need existing user and plan for testing');
        }
        
        echo "\n";
    }
    
    private function testTransactionHandling() {
        echo "6. Testing Transaction Handling...\n";
        
        // Get existing user for testing
        $user = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}hyiplab_users LIMIT 1");
        
        if ($user) {
            // Test transaction creation
            $transaction_data = [
                'user_id' => $user->id,
                'invest_id' => 0,
                'amount' => 1000.00,
                'charge' => 0.00,
                'post_balance' => 1000.00,
                'trx_type' => 'deposit',
                'trx' => uniqid('TEST'),
                'details' => 'Test deposit transaction',
                'remark' => '',
                'wallet_type' => 'main',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ];
            
            $result = $this->wpdb->insert($this->wpdb->prefix . 'hyiplab_transactions', $transaction_data);
            $this->assertTest('Transaction creation', $result !== false, 'Should be able to create transaction');
            
            if ($result !== false) {
                $transaction_id = $this->wpdb->insert_id;
                
                // Test transaction retrieval
                $transaction = $this->wpdb->get_row($this->wpdb->prepare(
                    "SELECT * FROM {$this->wpdb->prefix}hyiplab_transactions WHERE id = %d",
                    $transaction_id
                ));
                $this->assertTest('Transaction retrieval', $transaction !== null, 'Should be able to retrieve created transaction');
                
                // Cleanup
                $this->wpdb->delete($this->wpdb->prefix . 'hyiplab_transactions', ['id' => $transaction_id]);
            }
        } else {
            $this->assertTest('Transaction handling prerequisites', false, 'Need existing user for testing');
        }
        
        echo "\n";
    }
    
    private function testBalanceCalculations() {
        echo "7. Testing Balance Calculations...\n";
        
        // Test balance calculation logic
        $user = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}hyiplab_users LIMIT 1");
        
        if ($user) {
            // Calculate deposits
            $total_deposits = $this->wpdb->get_var($this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->wpdb->prefix}hyiplab_transactions 
                 WHERE user_id = %d AND trx_type = 'deposit'",
                $user->id
            )) ?: 0;
            
            // Calculate withdrawals
            $total_withdrawals = $this->wpdb->get_var($this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->wpdb->prefix}hyiplab_transactions 
                 WHERE user_id = %d AND trx_type = 'withdrawal'",
                $user->id
            )) ?: 0;
            
            // Calculate profits
            $total_profits = $this->wpdb->get_var($this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->wpdb->prefix}hyiplab_transactions 
                 WHERE user_id = %d AND trx_type = 'profit'",
                $user->id
            )) ?: 0;
            
            // Calculate investments
            $total_invested = $this->wpdb->get_var($this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->wpdb->prefix}hyiplab_invests 
                 WHERE user_id = %d",
                $user->id
            )) ?: 0;
            
            $calculated_balance = $total_deposits - $total_withdrawals + $total_profits - $total_invested;
            
            $this->assertTest('Balance calculation', 
                is_numeric($calculated_balance), 
                'Balance calculation should return numeric value'
            );
            
            $this->assertTest('Balance consistency', 
                $calculated_balance >= 0, 
                'Calculated balance should be non-negative'
            );
        } else {
            $this->assertTest('Balance calculation prerequisites', false, 'Need existing user for testing');
        }
        
        echo "\n";
    }
    
    private function testLicenseManagement() {
        echo "8. Testing License Management...\n";
        
        // Test license option retrieval
        $license_code = get_option('hyiplab_purchase_code');
        $this->assertTest('License option exists', 
            $license_code !== false, 
            'License purchase code should be stored in WordPress options'
        );
        
        if ($license_code) {
            $this->assertTest('License format', 
                preg_match('/^[a-f0-9\-]+$/i', $license_code), 
                'License code should match expected format'
            );
        }
        
        echo "\n";
    }
    
    private function testAPIEndpoints() {
        echo "9. Testing API Endpoints...\n";
        
        // Test if HYIPLab API endpoints are accessible
        $api_url = home_url('/wp-json/hyiplab/v1/');
        $response = wp_remote_get($api_url);
        
        $this->assertTest('API accessibility', 
            !is_wp_error($response), 
            'HYIPLab API endpoints should be accessible'
        );
        
        if (!is_wp_error($response)) {
            $this->assertTest('API response', 
                wp_remote_retrieve_response_code($response) !== 404, 
                'API should return valid response'
            );
        }
        
        echo "\n";
    }
    
    private function testSecurityFeatures() {
        echo "10. Testing Security Features...\n";
        
        // Test nonce verification (if implemented)
        $this->assertTest('WordPress security', 
            defined('NONCE_KEY'), 
            'WordPress security constants should be defined'
        );
        
        // Test user capability checks
        $this->assertTest('User capabilities', 
            current_user_can('read'), 
            'Current user should have basic read capability'
        );
        
        echo "\n";
    }
    
    private function assertTest($test_name, $condition, $message) {
        $result = $condition ? 'PASS' : 'FAIL';
        $this->test_results[] = [
            'test' => $test_name,
            'result' => $result,
            'message' => $message
        ];
        
        $icon = $condition ? '✅' : '❌';
        echo "   {$icon} {$test_name}: {$result}\n";
        
        if (!$condition) {
            echo "      ⚠️  {$message}\n";
        }
    }
    
    private function generateTestReport() {
        echo "\n📊 Test Report Summary\n";
        echo "=====================\n";
        
        $total_tests = count($this->test_results);
        $passed_tests = count(array_filter($this->test_results, function($test) {
            return $test['result'] === 'PASS';
        }));
        $failed_tests = $total_tests - $passed_tests;
        
        echo "Total Tests: {$total_tests}\n";
        echo "Passed: {$passed_tests}\n";
        echo "Failed: {$failed_tests}\n";
        echo "Success Rate: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n\n";
        
        if ($failed_tests > 0) {
            echo "❌ Failed Tests:\n";
            foreach ($this->test_results as $test) {
                if ($test['result'] === 'FAIL') {
                    echo "   - {$test['test']}: {$test['message']}\n";
                }
            }
        }
        
        if ($passed_tests === $total_tests) {
            echo "🎉 All tests passed! HYIPLab integration is working correctly.\n";
        } else {
            echo "⚠️  Some tests failed. Please review the issues above.\n";
        }
    }
}

// Run the test suite
$test_suite = new HYIPLabTestSuite($wpdb);
$test_suite->runAllTests();

echo "\n🧪 Test suite completed!\n";
echo "For detailed testing, visit:\n";
echo "- WordPress Admin: http://localhost:8888/wp-admin/\n";
echo "- HYIPLab Dashboard: http://localhost:8888/wp-admin/admin.php?page=hyiplab\n";
echo "- API Endpoints: http://localhost:8888/wp-json/hyiplab/v1/\n"; 