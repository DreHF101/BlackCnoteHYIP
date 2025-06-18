--
-- Table structure for table `stakings`
--
CREATE TABLE {{prefix}}hyiplab_stakings (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `days` int NOT NULL,
    `interest_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
    `status` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) {{collate}};

-- --------------------------------------------------------

--
-- Table structure for table `staking_invests`
--
CREATE TABLE {{prefix}}hyiplab_staking_invests (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `staking_id` int NOT NULL DEFAULT 0,
    `invest_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
    `interest` decimal(28,8) NOT NULL DEFAULT 0.00000000,
    `end_at` datetime DEFAULT NULL,
    `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Running\r\n2: Completed',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) {{collate}};

-- --------------------------------------------------------

--
-- Table structure for table `promotion_tools`
--
--

CREATE TABLE {{prefix}}hyiplab_promotion_tools (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `banner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) {{collate}};

-- --------------------------------------------------------

--
-- Table structure for table `pools`
--
CREATE TABLE {{prefix}}hyiplab_pools (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `invested_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `interest_range` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `share_interest` tinyint NOT NULL DEFAULT '0',
  `interest` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

-- --------------------------------------------------------

--
-- Table structure for table `pool_invests`
--

CREATE TABLE {{prefix}}hyiplab_pool_invests (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `pool_id` int UNSIGNED NOT NULL,
  `invest_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: Running\r\n2: Completed\r\n',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

-- --------------------------------------------------------
--
-- Table structure for table `holidays`
--
CREATE TABLE IF NOT EXISTS {{prefix}}hyiplab_holidays (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

-- --------------------------------------------------------

--
-- Table structure for table `schedule_invests`
--

CREATE TABLE {{prefix}}hyiplab_schedule_invests (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL DEFAULT '0',
  `wallet` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `schedule_times` int NOT NULL DEFAULT '0',
  `rem_schedule_times` int NOT NULL DEFAULT '0',
  `interval_hours` int NOT NULL DEFAULT '0',
  `compound_times` int NOT NULL DEFAULT '0',
  `next_invest` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};