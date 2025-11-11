# eShop - Multi Vendor eCommerce Marketplace CMS

## Introduction

eShop is a modern multi-vendor marketplace platform where vendors, store owners, retailers, and wholesalers can showcase or sell their products in a single platform. It offers a powerful admin dashboard, seller panel, and delivery boy panel with comprehensive features for managing an online marketplace.

## Latest Version

**Current Version: v2.10.4** (Released: 20-Mar-2025)

## Demo Credentials

### Frontend Demo
- **URL**: [https://vendor.eshopweb.store/](https://vendor.eshopweb.store/)
- **Mobile**: 9974692496
- **Password**: 12345678

### Admin Dashboard Demo
- **URL**: [https://vendor.eshopweb.store/admin/login](https://vendor.eshopweb.store/admin/login)
- **Mobile**: 9876543210
- **Password**: 12345678

### Seller Dashboard Demo
- **URL**: [https://vendor.eshopweb.store/seller/login](https://vendor.eshopweb.store/seller/login)
- **Mobile**: 9988776655
- **Password**: 12345678

### Delivery Boy Dashboard Demo
- **URL**: [https://vendor.eshopweb.store/delivery_boy/login](https://vendor.eshopweb.store/delivery_boy/login)
- **Mobile**: 1234567890
- **Password**: 12345678

## Key Features

### Frontend Features
- **Elegant Home Page**: Eye-catchy and easy-to-access home screen with search options, sliders, sellers, dynamic product sections, and categories
- **Dynamic Product Sections**: Create sections like Newly Added Products, Products on Sale, Top Rated Products, Most Selling Products, and Custom Products
- **Advanced Product Listing**: List view and grid view display options with advanced filtering and sorting
- **Product Details**: Rich product details with images, videos, specifications, variants, reviews, and seller information
- **Product Swatches**: Color swatches, image swatches, and text swatches for product variants
- **Attributes, Tags, and Filters**: Create unlimited attributes for colors, sizes, brands, etc.
- **Zipcode Verification**: Check product deliverability in a specific area
- **Categories Management**: Unlimited categories with multi-level support
- **Promo Codes**: Manage and display promotional offers
- **Cart & Save for Later**: Server-side cart management across platforms
- **Multiple Addresses**: Manage multiple shipping addresses
- **Checkout Process**: Address selection, delivery time options, and various payment methods
- **Payment Methods**: COD, PayPal, Razorpay, Paystack, Flutterwave, Stripe, Paytm, PhonePe, Direct Bank Transfer, and more
- **Order Tracking**: Track orders from received to delivered state
- **Digital Wallet**: Built-in wallet functionality with transaction tracking
- **Multi-language Support**: LTR and RTL languages support
- **Themes**: Light and dark theme support
- **Customer Support**: Integrated ticket system

### Admin Dashboard Features
- **Powerful Dashboard**: Dashboard with analytics and module-wise permissions
- **Seller Management**: Approve/reject registration requests, manage products
- **Product Management**: Manage products, variants, attributes, taxes, and bulk upload
- **System Settings**: Store settings, payment methods, time slots, SMTP email settings
- **Marketing Tools**: Sliders, offers, and promotional banners
- **Support Ticket System**: Process customer support requests
- **Promo Code Management**: Create and manage smart promo codes with conditions
- **Delivery Boy Management**: Manage delivery personnel and their payments
- **Web Settings**: Customize general settings, themes, languages, and authentication
- **User Management**: Create system users with different roles and permissions

### Seller Panel Features
- **Product Management**: Manage products and categories
- **Order Management**: Process orders and shipments
- **Media Management**: Manage product images and media files
- **Financial Tools**: Track wallet transactions and withdrawal requests
- **Shipping Management**: Create parcels, update shipping status, and manage returns

### Delivery Boy Panel Features
- **Order Management**: Manage assigned orders
- **Earnings Tracking**: View earnings and transaction history

## System Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Proper file permissions
- SSL certificate (recommended for security)

## Installation

### Using Installation Wizard

1. Upload the code to your server on your desired domain/subdomain
2. Create a new database from your server's cPanel
3. Create a new user for the database & give all privileges
4. Visit `https://yourdomain.com/install` to start the installation wizard
5. Fill in the required details:
   - Database Hostname
   - Database Username
   - Database Password
   - Database Name
   - Admin Mobile Number
   - Admin Password
6. Click Install, and your system will be ready to use

### Manual Installation

1. Open `application/config/config.php` and set your domain URL in base_url
2. Open `application/config/database.php` and set your database credentials
3. Import the blank database into phpMyAdmin (found in eShop Multi Vendor - blank database - vX.X.X)
4. Delete the install folder from your server
5. Access your system using default credentials:
   - Mobile: 9876543210
   - Password: 12345678

## Updating the System

### Auto Update

1. Find the update file (update from vX.X to vX.X.X.zip) from your downloaded PHP source code
2. Visit `http://yourdomain.com/admin/updater`
3. Upload the update.zip file
4. Click on "Update The System" button
5. That's it! Your system will be updated to the latest version

**Note**: If upgrading from a very old version, please update the system in sequence without skipping any version.

## Shipping Methods

The system supports two shipping methods:

1. **Local Shipping**: Assign your delivery boy for customer orders
2. **Standard Shipping**: Use third-party courier services like Shiprocket

### Shiprocket Integration

The system includes complete integration with Shiprocket for order fulfillment:
- Create Shiprocket account and recharge wallet
- Configure API in admin panel
- Add pickup locations
- Generate AWB codes, labels, and invoices for shipments
- Track shipments directly from the admin panel

## Common Issues and Solutions

1. **404 Error**: Missing .htaccess file - upload it from the downloaded package
2. **Payment Settlement Issues**: Set up cron jobs properly or use manual settlement
3. **Products Not Showing**: Check activation status and disable only_full_group_by_mode in MySQL
4. **Installation Issues**: Make sure all fields are filled correctly
5. **SMTP Email Issues**: Configure email settings properly and test with external tools
6. **Push Notification Issues**: Set up FCM Server Key correctly
7. **Invalid Hash Error**: Check Client API Keys configuration

## Firebase Integration

For web version login, register, and password reset functionalities:

1. Create a Firebase project
2. Add Web App configuration
3. Set up Authentication methods
4. Configure authorized domains
5. Update Firebase settings in the eShop admin panel

## Social Login Configuration

The system supports social login integration:

1. Enable social login methods in admin panel (System > Store Settings)
2. Configure Firebase Authentication settings for Google and Facebook
3. Add your domain to authorized domains in Firebase console

## Technical Support

If you face any technical issues:

- **Web/Backend Issues**: Contact Foram Shah via Microsoft Teams
- **Mobile Issues**: Contact Shivani Bhanderi via Microsoft Teams
- Support is available from 9:00 AM to 6:00 PM IST (Monday to Friday)

## What's New in v2.10.4

- Added reason for return items
- Added image for return items
- Added particular seller sale report
- Improved order attachment flow
- Improved cash collection from delivery boy
- Improved seller deliverability flow
- Improved order tracking from admin side
- Fixed bugs and code improvements

## License

This product is licensed under the Envato Regular License or Extended License based on your purchase.

---

© 2025 eShop by Infinitietech. All rights reserved.

For documentation and updates, visit: [eShop Documentation](https://wrteamdev.github.io/eShop_Multivendor_App_Doc/web/index.html)