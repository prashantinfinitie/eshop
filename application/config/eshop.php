<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['system_modules'] = [
    'orders' =>  array('read', 'update', 'delete'),
    'profile' =>  array('read', 'update', 'delete'),
    'categories' =>  array('create', 'read', 'update', 'delete'),
    'brands' =>  array('create', 'read', 'update', 'delete'),
    'category_order' =>  array('read', 'update'),
    'product' => array('create', 'read', 'update', 'delete'),
    'media' => array('create', 'read', 'update', 'delete'),
    'product_order' => array('read', 'update'),
    'tax' => array('create', 'read', 'update', 'delete'),
    'attribute' => array('create', 'read', 'update', 'delete'),
    'attribute_set' => array('create', 'read', 'update', 'delete'),
    'attribute_value' => array('create', 'read', 'update', 'delete'),
    'home_slider_images' => array('create', 'read', 'update', 'delete'),
    'new_offer_images' => array('create', 'read', 'delete'),
    'promo_code' => array('create', 'read', 'update', 'delete'),
    'featured_section' => array('create', 'read', 'update', 'delete'),
    'customers' => array('read', 'update'),
    'return_request' => array('read', 'update'),
    'delivery_boy' => array('create', 'read', 'update', 'delete'),
    'fund_transfer' => array('create', 'read', 'update', 'delete'),
    'send_notification' => array('create', 'read', 'delete'),
    'notification_setting' => array('read', 'update'),
    'sms-gateway-settings' => array('read', 'update'),
    'client_api_keys' => array('create', 'read', 'update', 'delete'),
    'area' => array('create', 'read', 'update', 'delete'),
    'city' => array('create', 'read', 'update', 'delete'),
    'faq' => array('create', 'read', 'update', 'delete'),
    'zipcodes' => array('create', 'read', 'update', 'delete'),
    'support_tickets' => array('create', 'read', 'update', 'delete'),
    'settings' => array('read', 'update'),
    'affiliate_system' => array('read'),
    'affiliate_settings' => array('create', 'read', 'update', 'delete'),
    'affiliate_users' => array('create', 'read', 'update', 'delete'),
    'system_update' => array('update'),
    'seller' => array('create', 'read', 'update', 'delete'),
    'shipping_settings' => array('read', 'update'),
    'pickup_location' => array('create', 'read', 'update', 'delete'),
    'chat' => array('create', 'read', 'delete'),
    'system_user' => array('create', 'read', 'update', 'delete'),
];

$config['notification_modules'] = [
    'otp' => array('customer', 'notification_via_sms', 'notification_via_mail'),
    'place_order' => array('customer', 'notification_via_sms', 'notification_via_mail'),
    'seller_place_order' => array('seller', 'notification_via_sms', 'notification_via_mail'),
    'ticket_status' => array('customer', 'notification_via_sms', 'notification_via_mail'),
    'settle_cashback_discount' => array('customer', 'notification_via_sms', 'notification_via_mail'),
    'settle_seller_commission' => array('seller', 'notification_via_sms', 'notification_via_mail'),
    'customer_order_received' => array('customer', 'notification_via_sms', 'notification_via_mail'),
    'customer_order_processed' => array('customer', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'delivery_boy_order_processed' => array('delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'customer_order_shipped' => array('customer', 'notification_via_sms', 'notification_via_mail'),
    // 'delivery_boy_order_shipped' => array('delivery_boy','notification_via_sms','notification_via_mail'),
    'customer_order_delivered' => array('customer', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'customer_order_cancelled' => array('customer', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'customer_order_returned' => array('customer', 'seller', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'delivery_boy_return_order_assign' => array('delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'customer_order_returned_request_decline' => array('customer', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'customer_order_returned_request_approved' => array('customer', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'delivery_boy_order_deliver' => array('customer', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'wallet_transaction' => array('customer', 'admin', 'seller', 'delivery_boy', 'notification_via_sms', 'notification_via_mail'),
    'bank_transfer_receipt_status' => array('customer', 'notification_via_sms', 'notification_via_mail'),
    'bank_transfer_proof' => array('customer', 'admin', 'seller', 'notification_via_sms', 'notification_via_mail'),
];


$config['order_keys'] = ['order.id', 'order.user_id', 'order.address_id', 'order.mobile', 'order.total', 'order.delivery_charge', 'order.is_delivery_charge_returnable', 'order.wallet_balance', 'order.promo_code', 'order.promo_discount', 'order.discount', 'order.total_payable', 'order.payment_method', 'order.latitude', 'order.longitude', 'order.address', 'order.delivery_time', 'order.delivery_date', 'order.date_added', 'order.otp', 'order.notes', 'order.attachments', 'order.is_pos_order', 'user.id', 'user.ip_address', 'user.username', 'user.email', 'user.mobile', 'user.image', 'user.balance', 'user.active', 'user.company', 'user.address', 'user.bonus_type', 'user.bonus', 'user.cash_received', 'user.dob', 'user.city', 'user.area', 'user.street', 'user.pincode', 'user.serviceable_zipcodes', 'user.fcm_id', 'user.latitude', 'user.longitude', 'user.type', 'user.driving_license', 'user.status', 'user.web_fcm', 'user.created_on', 'addresses.id', 'addresses.user_id', 'addresses.name', 'addresses.type', 'addresses.mobile', 'addresses.alternate_mobile', 'addresses.address', 'addresses.landmark', 'addresses.area_id', 'addresses.city_id', 'addresses.city', 'addresses.area', 'addresses.pincode', 'addresses.country_code', 'addresses.state', 'addresses.country', 'addresses.latitude', 'addresses.longitude', 'addresses.is_default', 'transactions.id', 'transactions.transaction_type', 'transactions.user_id', 'transactions.order_id', 'transactions.order_item_id', 'transactions.type', 'transactions.txn_id', 'transactions.payu_txn_id', 'transactions.amount', 'transactions.status', 'transactions.currency_code', 'transactions.payer_email', 'transactions.message', 'transactions.transaction_date', 'transactions.date_created', 'transactions.is_refund', 'return_requests.id', 'return_requests.user_id', 'return_requests.product_id', 'return_requests.product_variant_id', 'return_requests.order_id', 'return_requests.order_item_id', 'return_requests.status', 'return_requests.remarks', 'return_requests.date_created'];


$config['type'] = array(
    'image' => array(
        'types' => array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'eps', 'svg'),
        'icon' => ''
    ),
    'video' => array(
        'types' => array('mp4', '3gp', 'avchd', 'avi', 'flv', 'mkv', 'mov', 'webm', 'wmv', 'mpg', 'mpeg', 'ogg'),
        'icon' => 'assets/admin/images/video-file.png'
    ),
    'document' => array(
        'types' => array('doc', 'docx', 'txt', 'pdf', 'ppt', 'pptx'),
        'icon' => 'assets/admin/images/doc-file.png'
    ),
    'spreadsheet' => array(
        'types' => array('xls', 'xsls'),
        'icon' => 'assets/admin/images/xls-file.png'
    ),
    'archive' => array(
        'types' => array('zip', '7z', 'bz2', 'gz', 'gzip', 'rar', 'tar'),
        'icon' => 'assets/admin/images/archive-file.png'
    )
);

$config['default_theme'] = 'classic';
$config['supported_locales'] = [
    "af" => "ZAR",
    "am" => "ETB",
    'ar_DZ' => "DZD",
    "ar_EG" => "EGP",
    "az" => "AZN",
    "be" => "BYN",
    "bg" => "BGN",
    "bn" => "BDT",
    "br" => "EUR",
    "bs" => "BAM",
    "ca" => "EUR",
    "chr" => "USD",
    "cs" => "CZK",
    "cy" => "GBP",
    "da" => "DKK",
    "de" => "EUR",
    "de_AT" => "EUR",
    "de_CH" => "CHF",
    "el" => "EUR",
    "en" => "USD",
    "en_AU" => "AUD",
    "en_CA" => "CAD",
    "en_GB" => "GBP",
    "en_IE" => "EUR",
    "en_IN" => "INR",
    "en_MY" => "MYR",
    "en_SG" => "SGD",
    "en_US" => "USD",
    "en_ZA" => "ZAR",
    "es" => "EUR",
    "es_419" => "MXN",
    "es_ES" => "EUR",
    "es_MX" => "MXN",
    "es_US" => "USD",
    "et" => "EUR",
    "eu" => "EUR",
    "fi" => "EUR",
    "fil" => "PHP",
    "fr" => "EUR",
    "fr_CA" => "CAD",
    "fr_CH" => "CHF",
    "ga" => "EUR",
    "gl" => "EUR",
    "gsw" => "CHF",
    "gu" => "INR",
    "haw" => "USD",
    "he" => "ILS",
    "hi" => "INR",
    "hr" => "HRK",
    "hu" => "HUF",
    "hy" => "AMD",
    "id" => "IDR",
    "in" => "IDR",
    "is" => "ISK",
    "it" => "EUR",
    "it_CH" => "CHF",
    "iw" => "ILS",
    "ja" => "JPY",
    "ka" => "GEL",
    "kk" => "KZT",
    "km" => "KHR",
    "kn" => "INR",
    "ko" => "KRW",
    "ky" => "KGS",
    "ln" => "CDF",
    "lo" => "LAK",
    "lt" => "EUR",
    "lv" => "EUR",
    "mk" => "MKD",
    "ml" => "INR",
    "mn" => "MNT",
    "mr" => "INR",
    "ms" => "MYR",
    "mt" => "EUR",
    "nb" => "NOK",
    "ne" => "NPR",
    "nl" => "EUR",
    "no" => "NOK",
    "no_NO" => "NOK",
    "or" => "INR",
    "pa" => "INR",
    "pl" => "PLN",
    "pt" => "BRL",
    "pt_BR" => "BRL",
    "pt_PT" => "EUR",
    "ro" => "RON",
    "ru" => "RUB",
    "si" => "LKR",
    "sk" => "EUR",
    "sl" => "EUR",
    "sq" => "ALL",
    "sr" => "RSD",
    "sr_Latn" => "RSD",
    "sv" => "SEK",
    "sw" => "TZS",
    "ta" => "INR",
    "te" => "INR",
    "th" => "THB",
    "tl" => "PHP",
    "tr" => "TRY",
    "uk" => "UAH",
    "ur" => "PKR",
    "uz" => "UZS",
    "vi" => "VND",
    "zh" => "CNY",
    "zh_CN" => "CNY",
    "zh_HK" => "HKD",
    "zh_TW" => "TWD",
    "zu" => "ZAR"
];

$config['supported_locales_list'] = [
    "AED" => "United Arab Emirates Dirham",
    "AFN" => "Afghanistan Afghani",
    "ALL" => "Albania Lek",
    "AMD" => "Armenia Dram",
    "ANG" => "Netherlands Antilles Guilder",
    "AOA" => "Angola Kwanza",
    "ARS" => "Argentina Peso",
    "AUD" => "Australia Dollar",
    "AWG" => "Aruba Guilder",
    "AZN" => "Azerbaijan Manat",
    "BAM" => "Bosnia and Herzegovina Convertible Mark",
    "BBD" => "Barbados Dollar",
    "BDT" => "Bangladesh Taka",
    "BGN" => "Bulgaria Lev",
    "BHD" => "Bahrain Dinar",
    "BIF" => "Burundi Franc",
    "BMD" => "Bermuda Dollar",
    "BND" => "Brunei Darussalam Dollar",
    "BOB" => "Bolivia Bolíviano",
    "BRL" => "Brazil Real",
    "BSD" => "Bahamas Dollar",
    "BTN" => "Bhutan Ngultrum",
    "BWP" => "Botswana Pula",
    "BYN" => "Belarus Ruble",
    "BZD" => "Belize Dollar",
    "CAD" => "Canada Dollar",
    "CDF" => "Congo/Kinshasa Franc",
    "CHF" => "Switzerland Franc",
    "CLP" => "Chile Peso",
    "CNY" => "China Yuan Renminbi",
    "COP" => "Colombia Peso",
    "CRC" => "Costa Rica Colon",
    "CUC" => "Cuba Convertible Peso",
    "CUP" => "Cuba Peso",
    "CVE" => "Cape Verde Escudo",
    "CZK" => "Czech Republic Koruna",
    "DJF" => "Djibouti Franc",
    "DKK" => "Denmark Krone",
    "DOP" => "Dominican Republic Peso",
    "DZD" => "Algeria Dinar",
    "EGP" => "Egypt Pound",
    "ERN" => "Eritrea Nakfa",
    "ETB" => "Ethiopia Birr",
    "EUR" => "Euro Member Countries",
    "FJD" => "Fiji Dollar",
    "FKP" => "Falkland Islands (Malvinas) Pound",
    "GBP" => "United Kingdom Pound",
    "GEL" => "Georgia Lari",
    "GGP" => "Guernsey Pound",
    "GHS" => "Ghana Cedi",
    "GIP" => "Gibraltar Pound",
    "GMD" => "Gambia Dalasi",
    "GNF" => "Guinea Franc",
    "GTQ" => "Guatemala Quetzal",
    "GYD" => "Guyana Dollar",
    "HKD" => "Hong Kong Dollar",
    "HNL" => "Honduras Lempira",
    "HRK" => "Croatia Kuna",
    "HTG" => "Haiti Gourde",
    "HUF" => "Hungary Forint",
    "IDR" => "Indonesia Rupiah",
    "ILS" => "Israel Shekel",
    "IMP" => "Isle of Man Pound",
    "INR" => "India Rupee",
    "IQD" => "Iraq Dinar",
    "IRR" => "Iran Rial",
    "ISK" => "Iceland Krona",
    "JEP" => "Jersey Pound",
    "JMD" => "Jamaica Dollar",
    "JOD" => "Jordan Dinar",
    "JPY" => "Japan Yen",
    "KES" => "Kenya Shilling",
    "KGS" => "Kyrgyzstan Som",
    "KHR" => "Cambodia Riel",
    "KMF" => "Comorian Franc",
    "KPW" => "Korea (North) Won",
    "KRW" => "Korea (South) Won",
    "KWD" => "Kuwait Dinar",
    "KYD" => "Cayman Islands Dollar",
    "KZT" => "Kazakhstan Tenge",
    "LAK" => "Laos Kip",
    "LBP" => "Lebanon Pound",
    "LKR" => "Sri Lanka Rupee",
    "LRD" => "Liberia Dollar",
    "LSL" => "Lesotho Loti",
    "LYD" => "Libya Dinar",
    "MAD" => "Morocco Dirham",
    "MDL" => "Moldova Leu",
    "MGA" => "Madagascar Ariary",
    "MKD" => "Macedonia Denar",
    "MMK" => "Myanmar (Burma) Kyat",
    "MNT" => "Mongolia Tughrik",
    "MOP" => "Macau Pataca",
    "MRU" => "Mauritania Ouguiya",
    "MUR" => "Mauritius Rupee",
    "MVR" => "Maldives (Maldive Islands) Rufiyaa",
    "MWK" => "Malawi Kwacha",
    "MXN" => "Mexico Peso",
    "MYR" => "Malaysia Ringgit",
    "MZN" => "Mozambique Metical",
    "NAD" => "Namibia Dollar",
    "NGN" => "Nigeria Naira",
    "NIO" => "Nicaragua Cordoba",
    "NOK" => "Norway Krone",
    "NPR" => "Nepal Rupee",
    "NZD" => "New Zealand Dollar",
    "OMR" => "Oman Rial",
    "PAB" => "Panama Balboa",
    "PEN" => "Peru Sol",
    "PGK" => "Papua New Guinea Kina",
    "PHP" => "Philippines Peso",
    "PKR" => "Pakistan Rupee",
    "PLN" => "Poland Zloty",
    "PYG" => "Paraguay Guarani",
    "QAR" => "Qatar Riyal",
    "RON" => "Romania Leu",
    "RSD" => "Serbia Dinar",
    "RUB" => "Russia Ruble",
    "RWF" => "Rwanda Franc",
    "SAR" => "Saudi Arabia Riyal",
    "SBD" => "Solomon Islands Dollar",
    "SCR" => "Seychelles Rupee",
    "SDG" => "Sudan Pound",
    "SEK" => "Sweden Krona",
    "SGD" => "Singapore Dollar",
    "SHP" => "Saint Helena Pound",
    "SLL" => "Sierra Leone Leone",
    "SOS" => "Somalia Shilling",
    "SPL*" => "Seborga Luigino",
    "SRD" => "Suriname Dollar",
    "STN" => "São Tomé and Príncipe Dobra",
    "SVC" => "El Salvador Colon",
    "SYP" => "Syria Pound",
    "SZL" => "eSwatini Lilangeni",
    "THB" => "Thailand Baht",
    "TJS" => "Tajikistan Somoni",
    "TMT" => "Turkmenistan Manat",
    "TND" => "Tunisia Dinar",
    "TOP" => "Tonga Pa'anga",
    "TRY" => "Turkey Lira",
    "TTD" => "Trinidad and Tobago Dollar",
    "TVD" => "Tuvalu Dollar",
    "TWD" => "Taiwan New Dollar",
    "TZS" => "Tanzania Shilling",
    "UAH" => "Ukraine Hryvnia",
    "UGX" => "Uganda Shilling",
    "USD" => "United States Dollar",
    "UYU" => "Uruguay Peso",
    "UZS" => "Uzbekistan Som",
    "VEF" => "Venezuela Bolívar",
    "VND" => "Viet Nam Dong",
    "VUV" => "Vanuatu Vatu",
    "WST" => "Samoa Tala",
    "XAF" => "Communauté Financière Africaine (BEAC) CFA Franc BEAC",
    "XCD" => "East Caribbean Dollar",
    "XDR" => "International Monetary Fund (IMF) Special Drawing Rights",
    "XOF" => "Communauté Financière Africaine (BCEAO) Franc",
    "XPF" => "Comptoirs Français du Pacifique (CFP) Franc",
    "YER" => "Yemen Rial",
    "ZAR" => "South Africa Rand",
    "ZMW" => "Zambia Kwacha",
    "ZWD" => "Zimbabwe Dollar"
];


$config['decimal_point'] = array("0", "1", "2");
$config['supported_payment_methods'] = array("paypal", "razorpay", "paystack", "stripe", "flutterwave", "paytm", "midtrans", 'instamojo', 'phonepe');
$config['system_user_roles'] = array("super_admin", "admin", "editor", "supporter");

$config['shiprocket_status_codes'] = [
    ["code" => 3, "description" => "pickup generated"],
    ["code" => 62, "description" => "ready to pack"],
    ["code" => 42, "description" => "picked up"],
    ["code" => 6, "description" => "shipped"],
    ["code" => 17, "description" => "out for delivery"],
    ["code" => 7, "description" => "delivered"],
    ["code" => 8, "description" => "cancelled"],
    ["code" => 16, "description" => "cancellation requested"],
    ["code" => 23, "description" => "partial delivered"],
];

$status = [
    'received' => 0,
    'processed' => 1,
    'shipped' => 2,
    'delivered' => 3,
    'return_request_pending' => 4,
    'return_request_approved' => 5,
    'cancelled' => 6,
    'returned' => 7,
];

$config['shiprocket_status'] = [

    "Pickup Error",
    "ReadyForReceive" => "received",


    "Box Packing",
    "Ready To Pack",
    "Pickup Scheduled",
    "Packed",
    "PACKED EXCEPTION",
    "Out For Pickup",
    "Pickup Exception" => "processed",


    "Pickup Booked",
    "REACHED_BACK_AT_SELLER_CITY" => "return_request_approved",


    "PICKED UP",
    "Shipped",
    "Pickup Rescheduled",
    "Out For Delivery",
    "In Transit",
    "Delayed",
    "REACHED AT DESTINATION HUB",
    "MISROUTED",
    "Reached Warehouse",
    "Custom Cleared",
    "In Flight",
    "Handover to Courier",
    "Shipment Booked",
    "In Transit Overseas",
    "Connection Aligned",
    "Reached Overseas Warehouse",
    "Custom Cleared Overseas",
    "PROCESSED AT WAREHOUSE",
    "RIDER ASSIGNED",
    "RIDER UNASSIGNED",
    "RIDER REACHED AT DROP",
    "SEARCHING_FOR_RIDER",
    "Picklist Generated",
    "FC Allocated",
    "FC MANIFEST GENERATED" => "shipped",


    "DELIVERED",
    "Partial_Delivered",
    "FULFILLED",
    "SELF FULFILLED",
    "Pickup Error" => "delivered",


    "Canceled",
    "Cancellation Requested",
    "Lost",
    "UNTRACEABLE" => "cancelled",


    "RTO Initiated",
    "RTO Delivered",
    "RTO Acknowledged",
    "RTO_NDR",
    "RTO_OFD",
    "DAMAGED",
    "DESTROYED",
    "DISPOSED OFF",
    "CANCELLED_BEFORE_DISPATCHED",
    "RTO IN INTRANSIT",
    "QC FAILED",
    "HANDOVER EXCEPTION",
    "RTO_LOCK",
    "ISSUE_RELATED_TO_THE_RECIPIENT",
    "Undelivered" => "return_request_pending",

];


/* $config['shiprocket_status'] = [

    //received 
    13 => "Pickup Error",

    
    //processed
    59 => "Box Packing", // The shipment is being prepared or packed.
    62 => "Ready To Pack", // The shipment is ready to be packed.
    63 => "Packed", // The shipment has been packed.
    72 => "PACKED EXCEPTION", // There was an exception during packing.


    //Shipped
    6  => "Shipped",
    15 => "Pickup Rescheduled",
    17 => "Out For Delivery",
    18 => "In Transit", // The shipment is in transit to its destination.
    22 => "Delayed", // The shipment has been delayed.
    38 => "REACHED AT DESTINATION HUB", // The shipment has reached the destination hub.
    39 => "MISROUTED", // The shipment has been sent to the wrong location.
    48 => "Reached Warehouse", // The shipment has reached the warehouse.
    49 => "Custom Cleared", // The shipment has cleared customs.
    50 => "In Flight", // The shipment is in transit via air.
    51 => "Handover to Courier", // The shipment has been handed over to the courier.
    52 => "Shipment Booked", // The shipment has been booked in the system.
    54 => "In Transit Overseas", // The shipment is in transit to an international destination.
    55 => "Connection Aligned", // The shipment's connection to the next transit point has been aligned.
    56 => "Reached Overseas Warehouse", // The shipment has reached an overseas warehouse.
    57 => "Custom Cleared Overseas", // The shipment has cleared customs in the destination country.
    68 => "PROCESSED AT WAREHOUSE", // The shipment has been processed at the warehouse.
    79 => "RIDER ASSIGNED", // A delivery rider has been assigned to the shipment.
    80 => "RIDER UNASSIGNED", // The assigned rider has been unassigned from the shipment.
    82 => "RIDER REACHED AT DROP", // The rider has reached the drop-off location.
    83 => "SEARCHING_FOR_RIDER", // The system is searching for a rider to deliver the shipment.
    61 => "Picklist Generated", // A picklist has been generated for the shipment.
    60 => "FC Allocated", // Fulfillment center has been allocated for the shipment.
    67 => "FC MANIFEST GENERATED", // The manifest has been generated at the fulfillment center.

    

    //Delivered
    7 => "Delivered",
    23 => "Partial_Delivered", // Only part of the shipment has been delivered.
    26 => "FULFILLED", // The shipment has been successfully fulfilled.
    43 => "SELF FULFILLED", // The shipment was fulfilled by the seller without using courier services.
    13 => "Pickup Error", // An error occurred during the pickup process.
    

    //Cancelled
    8 => "Canceled",
    16 => "Cancellation Requested",
    12 => "Lost", // The shipment has been marked as lost.
    76 => "UNTRACEABLE", // The shipment is currently untraceable.
    
    //return_request_pending
    9 => "RTO Initiated",
    10 => "RTO Delivered",
    14 => "RTO Acknowledged",
    40 => "RTO_NDR", // Non-Delivery Report for an RTO shipment.
    41 => "RTO_OFD", // RTO shipment is out for delivery.
    25 => "DAMAGED", // The shipment has been damaged.
    24 => "DESTROYED", // The shipment has been destroyed.
    44 => "DISPOSED OFF", // The shipment has been disposed of.
    45 => "CANCELLED_BEFORE_DISPATCHED", // The shipment was canceled before being dispatched.
    46 => "RTO IN INTRANSIT", // The RTO shipment is in transit back to the origin.
    47 => "QC FAILED", // The shipment failed quality control checks.
    71 => "HANDOVER EXCEPTION", // There was an exception during the handover process.
    75 => "RTO_LOCK", // The RTO shipment has been locked for further action.
    77 => "ISSUE_RELATED_TO_THE_RECIPIENT", // There is an issue related to the recipient of the shipment.
    21 => "Undelivered", // The shipment could not be delivered to the recipient.
    

    //return_request_approved
    19 => "Out For Pickup", // The shipment is out for pickup by the courier.
    20 => "Pickup Exception", // There was an exception or issue during pickup.
    27 => "Pickup Booked", // The pickup for the shipment has been booked.
    42 => "PICKED UP", // The shipment has been picked up by the courier.
    78 => "REACHED_BACK_AT_SELLER_CITY", // The shipment has returned to the seller's city.
];
 */