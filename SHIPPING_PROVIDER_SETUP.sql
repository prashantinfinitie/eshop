<?php
/**
 * QUICK SETUP SQL SCRIPT
 *
 * Copy and run these SQL queries in your database to set up the shipping provider system.
 * Adjust values according to your needs.
 */

/**
 * =====================================================================
 * STEP 1: Add provider_type column to zipcodes table (if not exists)
 * =====================================================================
 */

-- ALTER TABLE `zipcodes` ADD COLUMN `provider_type` ENUM('company', 'delivery_boy') DEFAULT 'delivery_boy' AFTER `id`;

/**
 * =====================================================================
 * STEP 2: Set Provider Types for Sample Zipcodes
 * =====================================================================
 */

-- For zipcodes using shipping companies
-- UPDATE `zipcodes` SET `provider_type` = 'company' WHERE `zipcode` IN ('10001', '10002', '10003');

-- For zipcodes using delivery boys
-- UPDATE `zipcodes` SET `provider_type` = 'delivery_boy' WHERE `zipcode` IN ('10004', '10005', '10006');

/**
 * =====================================================================
 * STEP 3: Add Sample Shipping Company Quotes
 * =====================================================================
 */

-- INSERT INTO `shipping_company_quotes`
-- (`shipping_company_id`, `zipcode`, `price`, `eta_text`, `cod_available`, `is_active`, `created_at`, `updated_at`)
-- VALUES
-- (1, '10001', 50.00, '3-5 days', 1, 1, NOW(), NOW()),
-- (2, '10001', 45.00, '2-3 days', 1, 1, NOW(), NOW()),
-- (3, '10001', 55.00, '4-6 days', 0, 1, NOW(), NOW()),
-- (1, '10002', 40.00, '2-4 days', 1, 1, NOW(), NOW()),
-- (2, '10002', 42.00, '1-2 days', 1, 1, NOW(), NOW());

/**
 * =====================================================================
 * STEP 4: Configure Delivery Boys for Sample Zipcodes
 * =====================================================================
 */

-- For delivery boy with ID 1, make them service these zipcodes
-- UPDATE `users` SET `serviceable_zipcodes` = '10004,10005,10006' WHERE `id` = 1 AND `group_id` = 3;

-- For delivery boy with ID 2, make them service these zipcodes
-- UPDATE `users` SET `serviceable_zipcodes` = '10004,10005,10007' WHERE `id` = 2 AND `group_id` = 3;

/**
 * =====================================================================
 * STEP 5: Verify Shipping Companies Exist
 * =====================================================================
 */

-- Check if shipping companies exist
-- SELECT * FROM `shipping_companies` WHERE `is_active` = 1;

-- If needed, add sample companies:
-- INSERT INTO `shipping_companies` (`company_name`, `is_active`, `created_at`)
-- VALUES
-- ('FedEx', 1, NOW()),
-- ('DHL', 1, NOW()),
-- ('UPS', 1, NOW());

/**
 * =====================================================================
 * STEP 6: Verify Delivery Boys Setup
 * =====================================================================
 */

-- Check delivery boys (users with group_id = 3)
-- SELECT `id`, `username`, `serviceable_zipcodes`, `active`, `status` FROM `users` WHERE `group_id` = 3;

/**
 * =====================================================================
 * STEP 7: Test Data Verification
 * =====================================================================
 */

-- Check zipcodes with provider types
-- SELECT `id`, `zipcode`, `provider_type` FROM `zipcodes` LIMIT 10;

-- Check shipping company quotes
-- SELECT scq.*, sc.company_name FROM `shipping_company_quotes` scq
-- LEFT JOIN `shipping_companies` sc ON sc.id = scq.shipping_company_id
-- ORDER BY scq.zipcode, scq.price;

-- Check orders have new columns (after running migration)
-- DESCRIBE `orders`;

?>
