<?php
/**
 * IMPLEMENTATION SUMMARY - SHIPPING COMPANY & DELIVERY BOY INTEGRATION
 *
 * This document provides a complete overview of the delivery provider selection
 * system that allows customers to choose between shipping companies and delivery boys
 * based on their selected address zipcode.
 */

/**
 * =====================================================================
 * 1. DATABASE SCHEMA
 * =====================================================================
 */

/**
 * ZIPCODES TABLE - Already exists, should have:
 * - id (INT PRIMARY KEY)
 * - zipcode (VARCHAR)
 * - delivery_charges (DECIMAL)
 * - provider_type ENUM('company', 'delivery_boy') ← KEY COLUMN
 * - area_id, city_id, etc.
 */

/**
 * SHIPPING_COMPANY_QUOTES TABLE - Already created with:
 * - id (INT PRIMARY KEY)
 * - shipping_company_id (INT)
 * - zipcode (VARCHAR) ← Used to match with customer's zipcode
 * - price (DECIMAL) ← Delivery charge for this company
 * - eta_text (VARCHAR) ← Example: "3-5 days"
 * - cod_available (TINYINT) ← Whether COD is allowed
 * - is_active (TINYINT)
 * - additional_charges (TEXT)
 * - created_at, updated_at
 */

/**
 * USERS TABLE - Already exists, delivery boys should have:
 * - id (INT PRIMARY KEY)
 * - username (VARCHAR)
 * - serviceable_zipcodes (TEXT) ← Comma-separated: "12345,12346,12347"
 * - group_id = 3 ← Identifies as delivery boy
 * - active = 1
 * - status = 1 ← Approved delivery boys only
 */

/**
 * ORDERS TABLE - NEW COLUMNS ADDED via Migration 050:
 * - delivery_provider_type ENUM('company', 'delivery_boy') ← Snapshot of chosen provider type
 * - delivery_provider_id (INT) ← ID of shipping company quote OR delivery boy user ID
 * - selected_delivery_charges (DECIMAL) ← Snapshot of charges at order time
 * - delivery_provider_name (VARCHAR) ← Snapshot of company/boy name at order time
 * - delivery_provider_eta (VARCHAR) ← Snapshot of ETA at order time
 * - Indexes: idx_delivery_provider_type, idx_delivery_provider_id
 */

/**
 * =====================================================================
 * 2. BACKEND FLOW
 * =====================================================================
 */

/**
 * STEP 1: Customer selects address at checkout
 * └─ Frontend emits change event on .address-radio
 *
 * STEP 2: Frontend calls API: POST /cart/get-delivery-options
 * └─ Parameters: address_id, order_total
 *
 * STEP 3: Cart Controller -> get_delivery_options()
 * └─ Validates address exists
 * └─ Extracts zipcode from address
 * └─ Passes to Delivery_provider_model
 *
 * STEP 4: Delivery_provider_model -> get_delivery_options()
 * ├─ Calls get_provider_by_zipcode($zipcode)
 * │  └─ Checks zipcodes.provider_type for this zipcode
 * │
 * ├─ If provider_type = 'company':
 * │  └─ Calls get_shipping_quotes_by_zipcode($zipcode)
 * │     └─ Query: SELECT from shipping_company_quotes WHERE zipcode
 * │        └─ Returns: [id, company_name, price, eta_text, cod_available]
 * │
 * └─ If provider_type = 'delivery_boy':
 *    └─ Calls get_delivery_boys_by_zipcode($zipcode)
 *       └─ Query: SELECT from users WHERE FIND_IN_SET(zipcode, serviceable_zipcodes)
 *          └─ Returns: [id, username, bonus_type, bonus]
 *
 * STEP 5: Returns JSON response to frontend with list of options
 *
 * STEP 6: Frontend displays options in delivery-options-container
 *
 * STEP 7: Customer selects one delivery option
 * └─ Frontend captures: delivery_provider (form value), data attributes (type, id, price)
 *
 * STEP 8: Customer fills all required fields and clicks "Place Order"
 *
 * STEP 9: Frontend submits checkout_form with all data including:
 * ├─ delivery_provider (radio button value - the quote/boy ID)
 * ├─ delivery_provider_type (data attribute - 'company' or 'delivery_boy')
 * ├─ delivery_provider_id (data attribute - the actual provider ID)
 * └─ delivery_charges (data attribute - the price)
 *
 * STEP 10: Cart Controller -> place_order()
 * ├─ Validates all form fields
 * ├─ Extracts delivery provider info from $_POST
 * ├─ If type='company': Fetches company name & ETA from shipping_company_quotes
 * ├─ If type='delivery_boy': Fetches boy name from users table
 * ├─ Stores SNAPSHOT in order (name, eta, type, id, charges)
 * └─ Passes to order_model->place_order($_POST)
 *
 * STEP 11: Order is created with delivery provider information stored
 */

/**
 * =====================================================================
 * 3. FILES CREATED/MODIFIED
 * =====================================================================
 */

/**
 * A. MODELS
 *
 * 1. /application/models/Delivery_provider_model.php [NEW]
 *    Methods:
 *    - get_provider_by_zipcode($zipcode)
 *    - get_shipping_quotes_by_zipcode($zipcode)
 *    - get_delivery_boys_by_zipcode($zipcode, $zipcode_id)
 *    - get_delivery_options($zipcode, $order_total)
 *    - validate_delivery_option($zipcode, $provider_id, $provider_type)
 */

/**
 * B. CONTROLLERS
 *
 * 1. /application/controllers/Cart.php [MODIFIED]
 *    New Method:
 *    - get_delivery_options() - API endpoint to fetch delivery options
 *
 *    Modified Method:
 *    - place_order() - Added delivery provider capture and snapshot logic
 *
 * 2. /application/controllers/Account.php [MODIFIED]
 *    New Methods:
 *    - add_address() - Create new address for customer
 *    - get_addresses() - Fetch all addresses for customer
 */

/**
 * C. MIGRATIONS
 *
 * 1. /application/migrations/050_add_delivery_provider_to_orders.php [NEW]
 *    Adds 5 columns to orders table:
 *    - delivery_provider_type
 *    - delivery_provider_id
 *    - selected_delivery_charges
 *    - delivery_provider_name
 *    - delivery_provider_eta
 */

/**
 * D. VIEWS
 *
 * 1. /application/views/front_end/modern/checkout.php [MODIFIED]
 *    New Section:
 *    - Delivery Options Section with dynamic loading
 *    - Shows shipping companies OR delivery boys based on zipcode
 *    - Displays price, ETA, COD availability
 */

/**
 * E. JAVASCRIPT
 *
 * 1. /assets/front_end/modern/js/checkout.js [MODIFIED]
 *    New Functions:
 *    - initCheckout() - Initialize all event handlers
 *    - loadDeliveryOptions(addressId, zipcode) - Fetch options from API
 *    - displayDeliveryOptions(options, providerType) - Show options to user
 *    - updateDeliveryDisplay(name, price, type) - Update sidebar
 *    - updateOrderTotal() - Recalculate total with delivery charges
 *    - addNewAddress() - Submit new address via modal
 *    - reloadAddresses() - Refresh address list
 */

/**
 * =====================================================================
 * 4. DATA FLOW EXAMPLE
 * =====================================================================
 */

/**
 * SCENARIO: Customer in zipcode 12345 where provider_type = 'company'
 *
 * zipcodes table:
 * ┌─────┬─────────┬──────────────────┬─────────────────┐
 * │ id  │ zipcode │ provider_type     │ delivery_charges│
 * ├─────┼─────────┼──────────────────┼─────────────────┤
 * │ 1   │ 12345   │ company           │ 0.00            │
 * └─────┴─────────┴──────────────────┴─────────────────┘
 *
 * shipping_company_quotes table:
 * ┌────┬─────────────────────┬─────────┬────────┬──────────────┬───────────────┐
 * │ id │ shipping_company_id │ zipcode │ price  │ eta_text     │ cod_available │
 * ├────┼─────────────────────┼─────────┼────────┼──────────────┼───────────────┤
 * │ 1  │ 1 (FedEx)           │ 12345   │ 50.00  │ 3-5 days     │ 1             │
 * │ 2  │ 2 (DHL)             │ 12345   │ 45.00  │ 2-3 days     │ 1             │
 * │ 3  │ 3 (UPS)             │ 12345   │ 55.00  │ 4-6 days     │ 0             │
 * └────┴─────────────────────┴─────────┴────────┴──────────────┴───────────────┘
 *
 * Frontend Response from get_delivery_options:
 * {
 *   "error": false,
 *   "provider_type": "company",
 *   "delivery_options": [
 *     {
 *       "id": 1,
 *       "provider_type": "company",
 *       "provider_id": 1,
 *       "provider_name": "FedEx",
 *       "price": "50.00",
 *       "eta_text": "3-5 days",
 *       "cod_available": 1
 *     },
 *     {
 *       "id": 2,
 *       "provider_type": "company",
 *       "provider_id": 2,
 *       "provider_name": "DHL",
 *       "price": "45.00",
 *       "eta_text": "2-3 days",
 *       "cod_available": 1
 *     },
 *     {
 *       "id": 3,
 *       "provider_type": "company",
 *       "provider_id": 3,
 *       "provider_name": "UPS",
 *       "price": "55.00",
 *       "eta_text": "4-6 days",
 *       "cod_available": 0
 *     }
 *   ]
 * }
 *
 * Frontend displays:
 * ┌─────────────────────────────────────────────┐
 * │ Available Shipping Companies:               │
 * ├─────────────────────────────────────────────┤
 * │ ◯ FedEx          3-5 days  COD  ₹50.00     │
 * │ ◯ DHL            2-3 days  COD  ₹45.00     │
 * │ ◯ UPS            4-6 days       ₹55.00     │
 * └─────────────────────────────────────────────┘
 *
 * Customer selects DHL (id=2)
 *
 * Order created with:
 * - delivery_provider_type = 'company'
 * - delivery_provider_id = 2
 * - delivery_provider_name = 'DHL' (snapshot)
 * - delivery_provider_eta = '2-3 days' (snapshot)
 * - selected_delivery_charges = 45.00
 */

/**
 * =====================================================================
 * 5. API ENDPOINTS REFERENCE
 * =====================================================================
 */

/**
 * ENDPOINT 1: Get Delivery Options
 *
 * URL: POST /cart/get-delivery-options
 *
 * Request:
 * {
 *   "address_id": 123,
 *   "order_total": 500.00
 * }
 *
 * Response Success:
 * {
 *   "error": false,
 *   "message": "Delivery options fetched successfully",
 *   "zipcode": "12345",
 *   "provider_type": "company",
 *   "delivery_options": [ {...} ],
 *   "csrfName": "...",
 *   "csrfHash": "..."
 * }
 *
 * Response Error:
 * {
 *   "error": true,
 *   "message": "Zipcode not found in serviceable areas",
 *   "provider_type": null,
 *   "delivery_options": [],
 *   "csrfName": "...",
 *   "csrfHash": "..."
 * }
 */

/**
 * ENDPOINT 2: Add New Address
 *
 * URL: POST /account/add-address
 *
 * Request:
 * {
 *   "address_type": "home",
 *   "address": "123 Main Street, Apt 4B",
 *   "city": "New York",
 *   "pincode": "10001"
 * }
 *
 * Response:
 * {
 *   "error": false,
 *   "message": "Address added successfully",
 *   "csrfName": "...",
 *   "csrfHash": "..."
 * }
 */

/**
 * ENDPOINT 3: Get All Addresses
 *
 * URL: GET /account/get-addresses
 *
 * Response:
 * {
 *   "error": false,
 *   "message": "Addresses fetched successfully",
 *   "addresses": [
 *     {
 *       "id": 1,
 *       "user_id": 5,
 *       "address_type": "home",
 *       "address": "123 Main Street",
 *       "city": "New York",
 *       "pincode": "10001",
 *       "is_default": 1
 *     }
 *   ],
 *   "csrfName": "...",
 *   "csrfHash": "..."
 * }
 */

/**
 * =====================================================================
 * 6. SETUP INSTRUCTIONS
 * =====================================================================
 */

/**
 * STEP 1: Run Migration
 * - Go to admin panel
 * - Navigate to System > Database Migrations
 * - Run migration "050_add_delivery_provider_to_orders"
 * - This adds 5 new columns to orders table
 *
 * STEP 2: Set Provider Types for Zipcodes
 * - Go to admin panel
 * - Navigate to Settings > Shipping > Zipcodes
 * - For each zipcode, set provider_type to either 'company' or 'delivery_boy'
 *
 * Example SQL:
 * UPDATE zipcodes SET provider_type = 'company' WHERE zipcode IN ('12345', '12346');
 * UPDATE zipcodes SET provider_type = 'delivery_boy' WHERE zipcode IN ('12347', '12348');
 *
 * STEP 3: Add Shipping Company Quotes
 * - Go to admin panel (Shipping Company > Add Quote)
 * - For each company and zipcode combination, add a quote with:
 *   - Shipping Company
 *   - Zipcode
 *   - Price
 *   - ETA (e.g., "3-5 days")
 *   - COD Available (Yes/No)
 *   - Active (Yes/No)
 *
 * STEP 4: Configure Delivery Boys
 * - Go to Users > Delivery Boys
 * - For each delivery boy, set:
 *   - serviceable_zipcodes: comma-separated list (e.g., "12347,12348,12349")
 *   - active: 1
 *   - status: 1 (approved)
 *   - group_id: 3 (delivery boy group)
 *
 * STEP 5: Test the Flow
 * - Login as customer
 * - Go to checkout
 * - Select address in a 'company' provider_type zipcode
 * - Verify shipping companies appear
 * - Select address in a 'delivery_boy' provider_type zipcode
 * - Verify delivery boys appear
 * - Complete order and verify data is stored in order record
 */

/**
 * =====================================================================
 * 7. TROUBLESHOOTING
 * =====================================================================
 */

/**
 * Q: Delivery options not loading
 * A: Check:
 *    - Address zipcode exists in zipcodes table
 *    - provider_type is set for that zipcode
 *    - If company: shipping_company_quotes exists for that zipcode
 *    - If delivery_boy: users with group_id=3 have zipcode in serviceable_zipcodes
 *
 * Q: Wrong providers showing
 * A: Verify:
 *    - Correct provider_type set in zipcodes for that zipcode
 *    - Shipping company quote is_active = 1
 *    - Delivery boy status = 1 and active = 1
 *
 * Q: Order not saving delivery provider info
 * A: Ensure:
 *    - Migration 050 has been run
 *    - All 5 columns exist in orders table
 *    - place_order() method is capturing $_POST['delivery_provider_type'] etc.
 *
 * Q: "No delivery options available" error
 * A: This is normal if:
 *    - Zipcode has no shipping companies configured
 *    - No delivery boys service that zipcode
 *    - Set up at least one provider for the zipcode
 */

/**
 * =====================================================================
 * 8. SECURITY CONSIDERATIONS
 * =====================================================================
 */

/**
 * ✓ CSRF Protection: All forms use CSRF tokens
 * ✓ Input Validation: All inputs validated via form_validation
 * ✓ XSS Protection: All inputs escaped via xss_clean
 * ✓ SQL Injection: Using CodeIgniter's query builder (parameterized queries)
 * ✓ Authentication: All endpoints check ion_auth->logged_in()
 * ✓ Authorization: Verified that address belongs to logged-in user
 * ✓ Data Snapshots: Order stores provider info snapshot (immutable record)
 */

/**
 * =====================================================================
 * 9. PERFORMANCE OPTIMIZATIONS
 * =====================================================================
 */

/**
 * Database Indexes Created:
 * - zipcodes.provider_type (for quick filtering)
 * - shipping_company_quotes.zipcode (for fast quote lookup)
 * - orders.delivery_provider_type (for admin reports)
 * - orders.delivery_provider_id (for tracking providers)
 *
 * Query Optimization:
 * - Uses FIND_IN_SET for delivery boy zipcode matching
 * - Joins with shipping_companies table for company name
 * - Caches settings using get_settings()
 */

?>
