<?php
// Configuration file for Monetworks website

// Email settings
define('CONTACT_EMAIL', 'tmulelu@gmail.com');
define('FROM_EMAIL', 'tmulelu@gmail.com');
define('FROM_NAME', 'Monetworks Website');

// Company information
define('COMPANY_NAME', 'Monetworks');
define('COMPANY_ADDRESS', '1230 Business District, Sandton, Johannesburg, South Africa, 2196');
define('COMPANY_PHONE', '+27 81 355 0730');
define('COMPANY_EMAIL', 'info@monetworks.co.za');

// Security settings
define('RATE_LIMIT_SECONDS', 60); // Minimum seconds between form submissions
define('MAX_MESSAGE_LENGTH', 5000);
define('MAX_NAME_LENGTH', 100);
define('MAX_SUBJECT_LENGTH', 200);

// Enable/disable features
define('ENABLE_LOGGING', true);
define('ENABLE_RATE_LIMITING', true);
define('ENABLE_HONEYPOT', true);

// Development settings (set to false in production)
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
