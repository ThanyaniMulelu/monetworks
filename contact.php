<?php
// Contact form handler for Monetworks website
// This script processes the contact form and sends emails to tmulelu@gmail.com

// Enable error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type
header('Content-Type: application/json');

// Allow CORS for frontend-backend interaction
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to send email
function sendContactEmail($formData) {
    $to = 'tmulelu@gmail.com';
    $subject = 'New Contact Form Submission - Monetworks';
    
    // Create email content
    $message = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #000; color: #FFD700; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #000; }
            .value { margin-top: 5px; padding: 10px; background: #fff; border-left: 4px solid #FFD700; }
            .footer { background: #000; color: #fff; padding: 15px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>📧 New Contact Form Submission</h2>
                <p>Monetworks - Software & Data Solutions</p>
            </div>
            <div class='content'>
                <p>You have received a new message through the Monetworks website contact form:</p>
                
                <div class='field'>
                    <div class='label'>👤 Name:</div>
                    <div class='value'>" . htmlspecialchars($formData['name']) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>📧 Email:</div>
                    <div class='value'>" . htmlspecialchars($formData['email']) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>📱 Phone:</div>
                    <div class='value'>" . (empty($formData['phone']) ? 'Not provided' : htmlspecialchars($formData['phone'])) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>🏢 Company:</div>
                    <div class='value'>" . (empty($formData['company']) ? 'Not provided' : htmlspecialchars($formData['company'])) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>📋 Subject:</div>
                    <div class='value'>" . htmlspecialchars($formData['subject']) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>💬 Message:</div>
                    <div class='value'>" . nl2br(htmlspecialchars($formData['message'])) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>🕒 Submitted:</div>
                    <div class='value'>" . date('Y-m-d H:i:s T') . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>🌐 IP Address:</div>
                    <div class='value'>" . $_SERVER['REMOTE_ADDR'] . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>This email was sent from the Monetworks website contact form.</p>
                <p>Please respond directly to the sender's email address: " . htmlspecialchars($formData['email']) . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: Monetworks Website <noreply@monetworks.co.za>',
        'Reply-To: ' . $formData['email'],
        'X-Mailer: PHP/' . phpversion(),
        'X-Priority: 3',
        'X-MSMail-Priority: Normal'
    );
    
    // Send email
    $success = mail($to, $subject, $message, implode("\r\n", $headers));
    
    return $success;
}

// Main processing logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
        $company = isset($_POST['company']) ? sanitizeInput($_POST['company']) : '';
        $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
        $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
        
        // Validation
        $errors = array();
        
        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!validateEmail($email)) {
            $errors[] = 'Please enter a valid email address';
        }
        
        if (empty($subject)) {
            $errors[] = 'Subject is required';
        }
        
        if (empty($message)) {
            $errors[] = 'Message is required';
        }
        
        // Check for spam (simple honeypot and rate limiting)
        if (isset($_POST['website']) && !empty($_POST['website'])) {
            // Honeypot field filled - likely spam
            $errors[] = 'Spam detected';
        }
        
        // Rate limiting (simple session-based)
        session_start();
        $current_time = time();
        $last_submission = isset($_SESSION['last_contact_submission']) ? $_SESSION['last_contact_submission'] : 0;
        
        if (($current_time - $last_submission) < 60) {
            $errors[] = 'Please wait at least 1 minute between submissions';
        }
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(array(
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ));
            exit();
        }
        
        // Prepare form data
        $formData = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'subject' => $subject,
            'message' => $message
        );
        
        // Send email
        $emailSent = sendContactEmail($formData);
        
        if ($emailSent) {
            // Update session for rate limiting
            $_SESSION['last_contact_submission'] = $current_time;
            
            // Log successful submission (optional)
            $logEntry = date('Y-m-d H:i:s') . " - Contact form submission from: " . $email . " (" . $name . ")\n";
            file_put_contents('contact_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
            
            http_response_code(200);
            echo json_encode(array(
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you soon.'
            ));
        } else {
            http_response_code(500);
            echo json_encode(array(
                'success' => false,
                'message' => 'Sorry, there was an error sending your message. Please try again later or contact us directly.'
            ));
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array(
            'success' => false,
            'message' => 'An unexpected error occurred. Please try again later.',
            'error' => $e->getMessage()
        ));
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(array(
        'success' => false,
        'message' => 'Method not allowed'
    ));
}
?>

