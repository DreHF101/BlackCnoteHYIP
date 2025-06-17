DROP TABLE IF EXISTS {{prefix}}hyiplab_deposits;
CREATE TABLE `{{prefix}}hyiplab_deposits` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `method_code` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `method_currency` varchar(40) DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `final_amo` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `detail` text DEFAULT NULL,
  `btc_amo` varchar(255) DEFAULT NULL,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `payment_try` int(10) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel',
  `from_api` tinyint(1) NOT NULL DEFAULT 0,
  `admin_feedback` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_extensions;
CREATE TABLE `{{prefix}}hyiplab_extensions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `script` text DEFAULT NULL,
  `shortcode` text DEFAULT NULL COMMENT 'object',
  `support` text DEFAULT NULL COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

INSERT INTO `{{prefix}}hyiplab_extensions` (`id`, `act`, `name`, `description`, `image`, `script`, `shortcode`, `support`, `status`, `created_at`, `updated_at`) VALUES
(2, 'google-recaptcha2', 'Google Recaptcha 2', 'Key location is shown bellow', 'recaptcha3.png', '\n<script src=\"https://www.google.com/recaptcha/api.js\"></script>\n<div class=\"g-recaptcha\" data-sitekey=\"{{site_key}}\" data-callback=\"verifyCaptcha\"></div>\n<div id=\"g-recaptcha-error\"></div>', '{\"site_key\":{\"title\":\"Site Key\",\"value\":\"6LdPC88fAAAAADQlUf_DV6Hrvgm-pZuLJFSLDOWV\"},\"secret_key\":{\"title\":\"Secret Key\",\"value\":\"6LdPC88fAAAAAG5SVaRYDnV2NpCrptLg2XLYKRKB\"}}', 'recaptcha.png', 0, '2019-10-18 11:16:05', '2023-01-14 22:28:49'),
(3, 'custom-captcha', 'Custom Captcha', 'Just put any random string', 'customcaptcha.png', NULL, '{\"random_key\":{\"title\":\"Random String\",\"value\":\"SecureString\"}}', 'na', 0, '2019-10-18 11:16:05', '2023-01-14 22:28:46');


DROP TABLE IF EXISTS {{prefix}}hyiplab_forms;
CREATE TABLE `{{prefix}}hyiplab_forms` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `form_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_gateways;
CREATE TABLE `{{prefix}}hyiplab_gateways` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code` int(10) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) NOT NULL DEFAULT 'NULL',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `gateway_parameters` text DEFAULT NULL,
  `supported_currencies` text DEFAULT NULL,
  `crypto` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: fiat currency, 1: crypto currency',
  `extra` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

INSERT INTO `{{prefix}}hyiplab_gateways` (`id`, `form_id`, `code`, `name`, `alias`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES
(1, 0, 101, 'Paypal', 'Paypal', 1, '{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 12:04:38'),
(2, 0, 102, 'Perfect Money', 'PerfectMoney', 1, '{\"passphrase\":{\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\",\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true},\"wallet_id\":{\"value\":\"\",\"title\":\"PM Wallet\",\"global\":false}}', '{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 13:35:33'),
(3, 0, 103, 'Stripe Hosted', 'Stripe', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 12:48:36'),
(4, 0, 104, 'Skrill', 'Skrill', 1, '{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 13:30:16'),
(5, 0, 105, 'PayTM', 'Paytm', 1, '{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}', '{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 15:00:44'),
(6, 0, 106, 'Payeer', 'Payeer', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}', 0, '{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}', NULL, '2019-09-14 01:14:22', '2022-08-27 22:11:14'),
(7, 0, 107, 'PayStack', 'Paystack', 1, '{\"public_key\":{\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\",\"title\":\"Public key\",\"global\":true},\"secret_key\":{\"value\":\"sk_test_8a0b1f199362d7acc9c390bff72c4e81f74e2ac3\",\"title\":\"Secret key\",\"global\":true}}', '{\"USD\":\"USD\",\"NGN\":\"NGN\"}', 0, '{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n', NULL, '2019-09-14 01:14:22', '2021-05-20 13:49:51'),
(8, 0, 108, 'VoguePay', 'Voguepay', 1, '{\"merchant_id\":{\"title\":\"MERCHANT ID\",\"global\":true,\"value\":\"demo\"}}', '{\"USD\":\"USD\",\"GBP\":\"GBP\",\"EUR\":\"EUR\",\"GHS\":\"GHS\",\"NGN\":\"NGN\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 13:22:38'),
(9, 0, 109, 'Flutterwave', 'Flutterwave', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}', '{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-06-04 23:37:45'),
(10, 0, 110, 'RazorPay', 'Razorpay', 1, '{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 14:51:32'),
(11, 0, 111, 'Stripe Storefront', 'StripeJs', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 12:53:10'),
(12, 0, 112, 'Instamojo', 'Instamojo', 1, '{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 14:56:20'),
(13, 0, 501, 'Blockchain', 'Blockchain', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}', '{\"BTC\":\"BTC\"}', 1, NULL, NULL, '2019-09-14 01:14:22', '2022-03-20 19:41:56'),
(15, 0, 503, 'CoinPayments', 'Coinpayments', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"93a1e014c4ad60a7980b4a7239673cb4\"}}', '{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"USDT.BEP20\":\"Tether USD (BSC Chain)\",\"USDT.ERC20\":\"Tether USD (ERC20)\",\"USDT.TRC20\":\"Tether USD (Tron/TRC20)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}', 1, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 14:07:14'),
(16, 0, 504, 'CoinPayments Fiat', 'CoinpaymentsFiat', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 14:07:44'),
(17, 0, 505, 'Coingate', 'Coingate', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2022-03-29 21:24:57'),
(18, 0, 506, 'Coinbase Commerce', 'CoinbaseCommerce', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n', 0, '{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}', NULL, '2019-09-14 01:14:22', '2021-05-20 14:02:47'),
(19, 0, 113, 'Paypal Express', 'PaypalSdk', 1, '{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 11:01:08'),
(20, 0, 114, 'Stripe Checkout', 'StripeV3', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, '{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}', NULL, '2019-09-14 01:14:22', '2021-05-20 12:58:38'),
(21, 0, 115, 'Mollie', 'Mollie', 1, '{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, '2019-09-14 01:14:22', '2021-05-20 14:44:45'),
(22, 0, 116, 'Cashmaal', 'Cashmaal', 1, '{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}', '{\"PKR\":\"PKR\",\"USD\":\"USD\"}', 0, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}', NULL, NULL, '2021-06-21 20:05:04'),
(23, 0, 119, 'Mercado Pago', 'MercadoPago', 1, '{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"APP_USR-7924565816849832-082312-21941521997fab717db925cf1ea2c190-1071840315\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2022-09-13 19:41:14'),
(24, 0, 120, 'Authorize.net', 'Authorize', 1, '{\"login_id\":{\"value\":\"59e4P9DBcZv\",\"title\":\"Login ID\",\"global\":true},\"transaction_key\":{\"value\":\"47x47TJyLw2E7DbR\",\"title\":\"Transaction Key\",\"global\":true}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2022-08-27 21:33:06'),
(25, 0, 121, 'NMI', 'NMI', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"2F822Rw39fx762MaV7Yy86jXGTC7sCDy\"}}', '{\"AED\":\"AED\",\"ARS\":\"ARS\",\"AUD\":\"AUD\",\"BOB\":\"BOB\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"RUB\":\"RUB\",\"SEC\":\"SEC\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, NULL, '2022-08-27 22:32:31');

DROP TABLE IF EXISTS {{prefix}}hyiplab_gateway_currencies;
CREATE TABLE `{{prefix}}hyiplab_gateway_currencies` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `symbol` varchar(40) DEFAULT NULL,
  `method_code` int(10) DEFAULT NULL,
  `gateway_alias` varchar(40) DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `image` varchar(255) DEFAULT NULL,
  `gateway_parameter` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_invests;
CREATE TABLE `{{prefix}}hyiplab_invests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT 0,
  `plan_id` int(10) UNSIGNED DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `interest` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `should_pay` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `paid` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `period` int(11) DEFAULT 0,
  `hours` varchar(40) NOT NULL,
  `time_name` varchar(40) NOT NULL,
  `return_rec_time` int(11) NOT NULL DEFAULT 0,
  `next_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_time` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `capital_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = YES & 0 = NO',
  `trx` varchar(40) DEFAULT NULL,
  `wallet_type` varchar(40) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_notification_templates;
CREATE TABLE `{{prefix}}hyiplab_notification_templates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `subj` varchar(255) DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `shortcodes` text DEFAULT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 1,
  `sms_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

INSERT INTO `{{prefix}}hyiplab_notification_templates` (`id`, `act`, `name`, `subj`, `email_body`, `sms_body`, `shortcodes`, `email_status`, `sms_status`, `created_at`, `updated_at`) VALUES
(3, 'DEPOSIT_COMPLETE', 'Deposit - Automated - Successful', 'Deposit Completed Successfully', '<div>Your deposit of <span>{{amount}} {{site_currency}}</span> is via  <span>{{method_name}} </span>has been completed Successfully.<span><br></span></div><div><span><br></span></div><div><span>Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge: {{charge}} {{site_currency}}</div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :  {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><span><br></span></div><div>Your current Balance is <span>{{post_balance}} {{site_currency}}</span></div><div><br></div>', '{{amount}} {{site_currency}} Deposit successfully by {{method_name}}', '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, 1, '2021-11-03 00:00:00', '2022-04-02 14:25:43'),
(4, 'DEPOSIT_APPROVE', 'Deposit - Manual - Approved', 'Your Deposit is Approved', '<div>Your deposit request of <span>{{amount}} {{site_currency}}</span> is via  <span>{{method_name}} </span>is Approved .<span><br></span></div><div><span><br></span></div><div><span>Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge: {{charge}} {{site_currency}}</div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :  {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><span><br></span></div><div>Your current Balance is <span>{{post_balance}} {{site_currency}}</span></div><div><br></div><div><br></div>', 'Admin Approve Your {{amount}} {{site_currency}} payment request by {{method_name}} transaction : {{trx}}', '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}', 1, 1, '2021-11-03 00:00:00', '2022-04-02 14:26:07'),
(5, 'DEPOSIT_REJECT', 'Deposit - Manual - Rejected', 'Your Deposit Request is Rejected', '<div>Your deposit request of <span>{{amount}} {{site_currency}}</span> is via  <span>{{method_name}} has been rejected</span>.<span><br></span></div><div><br></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :  {{method_name}}</div><div>Charge: {{charge}}</div><div><br></div><div><br></div><div>Transaction Number was : {{trx}}</div><div><br></div><div>if you have any queries, feel free to contact us.<br></div><br><div><br><br></div><span>{{rejection_message}}</span><br>', 'Admin Rejected Your {{amount}} {{site_currency}} payment request by {{method_name}}\r\n\r\n{{rejection_message}}', '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"rejection_message\":\"Rejection message by the admin\"}', 1, 1, '2021-11-03 00:00:00', '2022-04-04 15:45:27'),
(6, 'DEPOSIT_REQUEST', 'Deposit - Manual - Requested', 'Deposit Request Submitted Successfully', '<div>Your deposit request of <span>{{amount}} {{site_currency}}</span> is via  <span>{{method_name}} </span>submitted successfully<span> .<br></span></div><div><span><br></span></div><div><span>Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge: {{charge}} {{site_currency}}</div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}}<br></div><div>Pay via :  {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br></div>', '{{amount}} {{site_currency}} Deposit requested by {{method_name}}. Charge: {{charge}} . Trx: {{trx}}', '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}', 1, 1, '2021-11-03 00:00:00', '2022-04-02 14:29:19'),
(9, 'ADMIN_SUPPORT_REPLY', 'Support - Reply', 'Reply Support Ticket', '<div><p><span><span>A member from our support team has replied to the following ticket:</span></span></p><p><span><span><span><br></span></span></span></p><p><span>[Ticket#{{ticket_id}}] {{ticket_subject}}<br><br>Click here to reply:  {{link}}</span></p><p>----------------------------------------------</p><p>Here is the reply :<br></p><p>{{reply}}<br></p></div><div><br></div>', 'Your Ticket#{{ticket_id}} :  {{ticket_subject}} has been replied.', '{\"ticket_id\":\"ID of the support ticket\",\"ticket_subject\":\"Subject  of the support ticket\",\"reply\":\"Reply made by the admin\",\"link\":\"URL to view the support ticket\"}', 1, 1, '2021-11-03 00:00:00', '2022-03-20 08:47:51'),
(12, 'WITHDRAW_APPROVE', 'Withdraw - Approved', 'Withdraw Request has been Processed and your money is sent', '<div>Your withdraw request of <span>{{amount}} {{site_currency}}</span>  via  <span>{{method_name}} </span>has been Processed Successfully.<span><br></span></div><div><span><br></span></div><div><span>Details of your withdraw:<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge: {{charge}} {{site_currency}}</div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>You will get: {{method_amount}} {{method_currency}}<br></div><div>Via :  {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div>-----</div><div><br></div><div>Details of Processed Payment :</div><div><span>{{admin_details}}</span></div>', 'Admin Approve Your {{amount}} {{site_currency}} withdraw request by {{method_name}}. Transaction {{trx}}', '{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"admin_details\":\"Details provided by the admin\"}', 1, 1, '2021-11-03 00:00:00', '2022-03-20 08:50:16'),
(13, 'WITHDRAW_REJECT', 'Withdraw - Rejected', 'Withdraw Request has been Rejected and your money is refunded to your account', '<div>Your withdraw request of <span>{{amount}} {{site_currency}}</span>  via  <span>{{method_name}} </span>has been Rejected.<span><br></span></div><div><span><br></span></div><div><span>Details of your withdraw:<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge: {{charge}} {{site_currency}}</div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>You should get: {{method_amount}} {{method_currency}}<br></div><div>Via :  {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br></div><div>----</div><div><br></div><div>{{amount}} {{currency}} has been <span>refunded </span>to your account and your current Balance is <span>{{post_balance}}</span><span> {{site_currency}}</span></div><div><br></div><div>-----</div><div><br></div><div>Details of Rejection :</div><div><span>{{admin_details}}</span></div><div><br></div><div><br><br><br><br><br></div><div></div><div></div>', 'Admin Rejected Your {{amount}} {{site_currency}} withdraw request. Your Main Balance {{post_balance}}  {{method_name}} , Transaction {{trx}}', '{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after fter this action\",\"admin_details\":\"Rejection message by the admin\"}', 1, 1, '2021-11-03 00:00:00', '2022-03-20 08:57:46'),
(14, 'WITHDRAW_REQUEST', 'Withdraw - Requested', 'Withdraw Request Submitted Successfully', '<div>Your withdraw request of <span>{{amount}} {{site_currency}}</span>  via  <span>{{method_name}} </span>has been submitted Successfully.<span><br></span></div><div><span><br></span></div><div><span>Details of your withdraw:<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge: {{charge}} {{site_currency}}</div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>You will get: {{method_amount}} {{method_currency}}<br></div><div>Via :  {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br></div><div>Your current Balance is <span>{{post_balance}} {{site_currency}}</span></div><div><br></div><div><br><br><br></div>', '{{amount}} {{site_currency}} withdraw requested by {{method_name}}. You will get {{method_amount}} {{method_currency}} Trx: {{trx}}', '{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after fter this transaction\"}', 1, 1, '2021-11-03 00:00:00', '2022-03-20 16:39:03'),
(15, 'DEFAULT', 'Default Template', '{{subject}}', '{{message}}', '{{message}}', '{\"subject\":\"Subject\",\"message\":\"Message\"}', 1, 1, '2019-09-14 01:14:22', '2021-11-03 21:38:55'),
(18, 'PASSWORD_RESET', 'Reset password', 'Password Reset', '<div>\r\n<div><strong>Someone requested that the password be reset for the following account:</strong></div>\r\n</div>\r\n<div>\r\n<div>\r\n<div>\r\n\r\nIf this was a mistake, just ignore this email and nothing will happen.\r\n<div>\r\n<div>To reset your password, visit the following address:</div>\r\n<div><a href=\"{{verify_link}}\">{{verify_link}}</a></div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', 'Someone requested that the password be reset for the following account:\r\nIf this was a mistake, just ignore this email and nothing will happen.\r\nTo reset your password, visit the following address:\r\n{{verify_link}}', '{\"verify_link\" : \"Password rest verify link\"}', 1, 0, NULL, NULL),
(19, 'REGISTER', 'User Registration', 'Account Registration', '<div>\r\n<div><strong>Thank you for registering with {{site_name}}</strong></div>\r\n</div>\r\n<div></div>\r\n<div>\r\n<div>\r\n<div>Please click the link to verify your email :</div>\r\n</div>\r\n</div>\r\n<div><a href=\"{{verify_link}}\">{{verify_link}}</a></div>', 'Thank you for registering with {{site_name}}\r\nPlease click the link to verify your email :\r\n{{verify_link}}', '{\"verify_link\" : \"Email verification link\"}', 1, 0, NULL, NULL),
(20, 'INVESTMENT', 'Investment', 'Investment successfully completed', '<div>Your {{amount}} {{site_currency}} investment successfully completed.</div>\r\n<div></div>\r\n<div><strong>Details of your Investment:</strong></div>\r\n<div>Amount : {{amount}} {{site_currency}}</div>\r\n<div>Wallet type: {{wallet_type}}</div>\r\n<div>Plan name: {{plan_name}}</div>\r\n<div>Interest: {{interest_amount}} {{site_currency}}</div>\r\n<div>Duration: {{time}}</div>\r\n<div>Every {{time_name}}</div>\r\n<div>Transaction Number : {{trx}}</div>\r\n<div></div>\r\n<div>Your current Balance is {{post_balance}} {{site_currency}}</div>\r\n<div></div>\r\n<div></div>', 'Your {{amount}} {{site_currency}} investment successfully completed.\r\nDetails of your Investment:\r\nAmount : {{amount}} {{site_currency}}\r\nWallet type: {{wallet_type}}\r\nPlan name: {{plan_name}}\r\nInterest: {{interest_amount}} {{site_currency}}\r\nDuration: {{time}}\r\nEvery {{time_name}}\r\nTransaction Number : {{trx}}\r\nYour current Balance is {{post_balance}} {{site_currency}}', '{\r\n    \"trx\": \"Transaction number for the interest\",\r\n    \"amount\": \"Amount inserted by the user\",\r\n    \"plan_name\": \"Plan name\",\r\n    \"interest_amount\": \"Interest amount\",\r\n    \"time\": \"Plan duration\",\r\n    \"time_name\": \"Interest duration\",\r\n    \"wallet_type\": \"Invest wallet\",\r\n    \"post_balance\": \"Balance of the user after this transaction\"\r\n}', 1, 0, NULL, NULL),
(21, 'REFERRAL_COMMISSION', 'Referral Commission', 'Referral Commission', '<div>You got {{amount}} {{site_currency}} referral commission.</div>\r\n<div></div>\r\n<div><strong>Details of your referral commission:</strong></div>\r\n<div>Amount : {{amount}} {{site_currency}}</div>\r\n<div>{{type}} referral commission</div>\r\n<div>Transaction Number : {{trx}}</div>\r\n<div>{{level}} level referral commission.</div>\r\n<div></div>\r\n<div>Your current Balance is {{post_balance}} {{site_currency}}</div>\r\n<div></div>\r\n<div></div>', 'You got {{amount}} {{site_currency}} referral commission.\r\nDetails of your referral commission:\r\nAmount : {{amount}} {{site_currency}}\r\n{{type}} referral commission\r\nTransaction Number : {{trx}}\r\n{{level}} level referral commission.\r\nYour current Balance is {{post_balance}} {{site_currency}}', '{\r\n    \"trx\": \"Transaction number for the interest\",\r\n    \"amount\": \"Amount inserted by the user\",\r\n    \"plan_name\": \"Plan name\",\r\n    \"level\": \"Which level referral commission\",\r\n    \"post_balance\": \"Balance of the user after this transaction\"\r\n}', 1, 0, NULL, NULL),
(22, 'INTEREST', 'Interest', 'Interest added to your balance', '<div>You got {{amount}} {{site_currency}} interest.</div>\r\n<div></div>\r\n<div><strong>Details of your Interest:</strong></div>\r\n<div>Amount : {{amount}} {{site_currency}}</div>\r\n<div>Plan Name: {{plan_name}}</div>\r\n<div>Transaction Number : {{trx}}</div>\r\n<div></div>\r\n<div><strong>Your current Balance is {{post_balance}} {{site_currency}}</strong></div>\r\n<div></div>\r\n<div></div>', 'You got {{amount}} {{site_currency}} interest.\r\nDetails of your Interest:\r\nAmount : {{amount}} {{site_currency}}\r\nPlan Name: {{plan_name}}\r\nTransaction Number : {{trx}}\r\nYour current Balance is {{post_balance}} {{site_currency}}', '{\r\n    \"trx\": \"Transaction number for the interest\",\r\n    \"amount\": \"Amount inserted by the user\",\r\n    \"plan_name\": \"Plan name\",\r\n    \"post_balance\": \"Balance of the user after this transaction\"\r\n}', 1, 0, NULL, NULL);

DROP TABLE IF EXISTS {{prefix}}hyiplab_plans;
CREATE TABLE `{{prefix}}hyiplab_plans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `minimum` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `maximum` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `fixed_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `interest` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `interest_type` tinyint(1) DEFAULT 0 COMMENT '1 = ''%'' / 0 =''currency''',
  `time_setting_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `capital_back` tinyint(1) DEFAULT 0,
  `lifetime` tinyint(1) DEFAULT 0,
  `repeat_time` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_referrals;
CREATE TABLE `{{prefix}}hyiplab_referrals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `commission_type` varchar(40) DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 0,
  `percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_support_attachments;
CREATE TABLE `{{prefix}}hyiplab_support_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `support_message_id` int(10) UNSIGNED DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_support_messages;
CREATE TABLE `{{prefix}}hyiplab_support_messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `support_ticket_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_support_tickets;
CREATE TABLE `{{prefix}}hyiplab_support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `ticket` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Open, 1: Answered, 2: Replied, 3: Closed',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Low, 2 = medium, 3 = heigh',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_time_settings;
CREATE TABLE `{{prefix}}hyiplab_time_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `time` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_transactions;
CREATE TABLE `{{prefix}}hyiplab_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `invest_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx_type` varchar(40) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `remark` varchar(40) DEFAULT NULL,
  `wallet_type` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_withdrawals;
CREATE TABLE `{{prefix}}hyiplab_withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `method_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(40) DEFAULT NULL,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx` varchar(40) DEFAULT NULL,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `after_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `withdraw_information` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel,  ',
  `admin_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_withdraw_methods;
CREATE TABLE `{{prefix}}hyiplab_withdraw_methods` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `min_limit` decimal(28,8) DEFAULT 0.00000000,
  `max_limit` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `fixed_charge` decimal(28,8) DEFAULT 0.00000000,
  `rate` decimal(28,8) DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) {{collate}};

DROP TABLE IF EXISTS {{prefix}}hyiplab_user_rankings;
CREATE TABLE `{{prefix}}hyiplab_user_rankings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(11) DEFAULT 0,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minimum_invest` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `min_referral_invest` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `min_referral` int(11) NOT NULL DEFAULT 0,
  `bonus` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) {{collate}};

INSERT INTO `{{prefix}}hyiplab_gateways` (`id`, `form_id`, `code`, `name`, `alias`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES (NULL, '0', '122', 'BTCPay', 'BTCPay', '1', '{\"store_id\":{\"title\":\"Store Id\",\"global\":true,\"value\":\"-------\"},\"api_key\":{\"title\":\"Api Key\",\"global\":true,\"value\":\"------\"},\"server_name\":{\"title\":\"Server Name\",\"global\":true,\"value\":\"https:\\/\\/yourbtcpaserver.lndyn.com\"},\"secret_code\":{\"title\":\"Secret Code\",\"global\":true,\"value\":\"----------\"}}', '{\"BTC\":\"Bitcoin\",\"LTC\":\"Litecoin\"}', '1', '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.btcpay\"}}', NULL, NULL, NULL);

INSERT INTO `{{prefix}}hyiplab_gateways` (`id`, `form_id`, `code`, `name`, `alias`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES (NULL, '0', '123', 'Now payments hosted', 'NowPaymentsHosted', '1', '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"-------------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"--------------\"}}', '{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}', '1', '', NULL, NULL, '2023-02-14 10:42:09');

INSERT INTO `{{prefix}}hyiplab_gateways` (`id`, `form_id`, `code`, `name`, `alias`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES (NULL, '0', '509', 'Now payments checkout', 'NowPaymentsCheckout', '1', '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"-------------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"--------------\"}}', '{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}', '1', '', NULL, NULL, '2023-02-14 10:42:09');
