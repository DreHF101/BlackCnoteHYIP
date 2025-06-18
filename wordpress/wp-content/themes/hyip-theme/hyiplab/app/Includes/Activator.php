<?php

namespace Hyiplab\Includes;

class Activator
{
    public function activate()
    {
        if (!get_option('hyiplab_installed') || !get_option('hyiplab_version')) {
        global $wp_rewrite, $wpdb;
        $sql = file_get_contents(HYIPLAB_ROOT . 'db/hyiplab.sql');
        $sql = str_replace('{{prefix}}', $wpdb->prefix, $sql);
        $sql = str_replace('{{collate}}', $wpdb->get_charset_collate(), $sql);
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option('hyiplab_version', hyiplab_system_details()['version']);
        update_option('hyiplab_installed', 1);
        update_option('hyiplab_cur_text', 'USD');
        update_option('hyiplab_cur_sym', '$');
        update_option('hyiplab_email_template', $this->email_form());
        update_option('hyiplab_email_from', get_option('admin_email'));
        update_option('hyiplab_sms_from', get_bloginfo('name'));
        update_option('hyiplab_sms_body', 'hi {{fullname}} ({{username}}), {{message}}');
        update_option('hyiplab_email_notification', 1);
        update_option('hyiplab_sms_notification', 0);
        update_option('hyiplab_email_verification', 1);
        update_option('hyiplab_user_ranking', 1);
        update_option('hyiplab_registration_bonus', 0);
        update_option('hyiplab_balance_transfer', 0);
        update_option('hyiplab_balance_transfer_fixed_charge', 0);
        update_option('hyiplab_balance_transfer_percent_charge', 0);
        update_option('hyiplab_mail_config', ['name' => 'php']);
        update_option('hyiplab_sms_config', ['name' => 'clickatell', 'clickatell' => ['api_key' => '']]);
        $wp_rewrite->set_permalink_structure('/%year%/%monthnum%/%postname%/');
        } else {

            $installed_version = get_option('hyiplab_version');
            $current_version = hyiplab_system_details()['version'];
            
            if (version_compare($installed_version, $current_version, '<')) {

                $sql_changes = file_get_contents(HYIPLAB_ROOT . 'db/changes.sql');

                $sql_changes = str_replace('{{prefix}}', $wpdb->prefix, $sql_changes);
                $sql_changes = str_replace('{{collate}}', $wpdb->get_charset_collate(), $sql_changes);

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql_changes);

                $this->alterTable();

                update_option('hyiplab_version', hyiplab_system_details()['version']);

                $user_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->users}");
                foreach ($user_ids as $user_id) {

                    update_user_meta($user_id, 'hyiplab_kyc', '1');
                    
                }

            }
        
        }

        flush_rewrite_rules();
    }

    public function deactivate()
    {
        flush_rewrite_rules();
    }

    public function alterTable(){
        global $wpdb;
        $alter_sql = "
        ALTER TABLE {$wpdb->prefix}hyiplab_invests
            ADD COLUMN compound_times int NOT NULL DEFAULT 0 AFTER last_time,
            ADD COLUMN rem_compound_times int NOT NULL DEFAULT 0 AFTER compound_times,
            ADD COLUMN capital_back tinyint(1) NOT NULL DEFAULT 0 AFTER rem_compound_times,
            ADD COLUMN hold_capital tinyint(1) NOT NULL DEFAULT 0 AFTER capital_back;
        
        ALTER TABLE {$wpdb->prefix}hyiplab_plans
            ADD COLUMN compound_interest tinyint(1) NOT NULL DEFAULT 0 AFTER repeat_time,
            ADD COLUMN hold_capital tinyint(1) NOT NULL DEFAULT 0 AFTER compound_interest;
        
        ALTER TABLE {$wpdb->prefix}hyiplab_notification_templates
            ADD COLUMN firebase_status tinyint(1) NOT NULL DEFAULT '0' AFTER sms_status,
            ADD COLUMN firebase_body text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER firebase_status;
        
        INSERT INTO {$wpdb->prefix}hyiplab_notification_templates (`id`, `act`, `name`, `subj`, `email_body`, `sms_body`, `shortcodes`, `email_status`, `sms_status`, `firebase_status`, `firebase_body`, `created_at`, `updated_at`) VALUES
            (1, 'BAL_ADD', 'Balance - Added', 'Your Account has been Credited', '{{amount}} {{site_currency}} has been added to your account.', '{{amount}} {{site_currency}} credited to your account. Your Current Balance {{post_balance}} {{site_currency}} . Transaction: #{{trx}}. Admin Note is {{remark}}', '{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, 0, 0, NULL, '2021-11-03 06:00:00', '2022-04-02 20:18:28'),
            (2, 'BAL_SUB', 'Balance - Subtracted', 'Your Account has been Debited', '<div>{{amount}} {{site_currency}} has been subtracted from your account .</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><span>Your Current Balance is :</span><span>{{post_balance}} {{site_currency}}</span><br><div><span><br></span></div><div>Admin Note: {{remark}}</div>', '{{amount}} {{site_currency}} debited from your account. Your Current Balance {{post_balance}} {{site_currency}} . Transaction: #{{trx}}. Admin Note is {{remark}}', '{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, 0, 0, NULL, '2021-11-03 06:00:00', '2022-04-02 20:24:11');
        ";

        $queries = explode(';', $alter_sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $wpdb->query($query);
            }
        }
    }

    public function email_form()
    {
        $html = '&lt;table&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;
        &lt;table cellspacing=\&quot;0\&quot; cellpadding=\&quot;0\&quot;&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
        &lt;td width=\&quot;600\&quot;&gt;
        &lt;table class=\&quot;table-inner\&quot; width=\&quot;95%\&quot; cellspacing=\&quot;0\&quot; cellpadding=\&quot;0\&quot;&gt;
        &lt;tbody&gt;
                &lt;/tbody&gt;
        &lt;/table&gt;
        &lt;table class=\&quot;table-inner\&quot; width=\&quot;95%\&quot; cellspacing=\&quot;0\&quot; cellpadding=\&quot;0\&quot;&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
        &lt;td&gt;
        &lt;table width=\&quot;90%\&quot; cellspacing=\&quot;0\&quot; cellpadding=\&quot;0\&quot;&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;a href=\&quot;#\&quot;&gt;&lt;br /&gt;
                                                                                    &lt;img src=\&quot;https://i.imgur.com/Z1qtvtV.png\&quot; alt=\&quot;img\&quot;&gt;&lt;/p&gt;
        &lt;p&gt;&lt;/a&gt;&lt;/p&gt;
        &lt;p&gt;&lt;a href=\&quot;#\&quot;&gt;                                                                        &lt;/a&gt;&lt;/p&gt;
        &lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;Hello {{fullname}} ({{username}})&lt;br /&gt;-------------------------------&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;
        &lt;table&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
        &lt;/table&gt;
        &lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;{{message}}&lt;br /&gt;-------------------------------&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
        &lt;/table&gt;
        &lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;
        &lt;table&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td class=\&quot;preference-link\&quot;&gt;&copy; 2023 &lt;a href=\&quot;#\&quot;&gt;{{site_name}}&lt;/a&gt;&amp;nbsp;. All Rights Reserved.&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
        &lt;/table&gt;
        &lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
        &lt;/table&gt;
        &lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
        &lt;/table&gt;
        &lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
        &lt;td&gt;&lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
        &lt;/table&gt;';
        return $html;
    }
}