<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes directly
require 'smtp/src/Exception.php';
require 'smtp/src/PHPMailer.php';
require 'smtp/src/SMTP.php';

// Manually load environment variables
define('SMTP_USER', 'info@soilwrap.com');
define('SMTP_PASS', 'ST@info2024');
define('SMTP_HOST', 'smtp.hostinger.com'); // Replace with Hostinger SMTP host
define('SMTP_PORT', 587); // or 465, based on Hostinger's settings

// Sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

$name = sanitize_input($_POST['name']);
$email = sanitize_input($_POST['email']);
$contact = sanitize_input($_POST['contact']);
$subject = sanitize_input($_POST['subject']);
$state = sanitize_input($_POST['state']);
$message = sanitize_input($_POST['message']);

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = SMTP_HOST;                              // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = SMTP_USER;                              // Use defined constant for SMTP username
    $mail->Password   = SMTP_PASS;                              // Use defined constant for SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption
    $mail->Port       = SMTP_PORT;                              // TCP port to connect to

    // Recipients
    $mail->setFrom('info@soilwrap.com', 'Soilwrap Technologies'); // Set sender address
    $mail->addAddress('soilwrap@gmail.com', 'Lead');          // Add a recipient

    // Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body    = "<p>Name: $name</p>".
                     "<p>Email: $email</p>".
                     "<p>Contact: $contact</p>".
                     "<p>Subject: $subject</p>".
                     "<p>State: $state</p>".
                     "<p>Message: $message</p>";

    // Send the email
    $mail->send();
    
    // Auto-response email
    $autoRespond = new PHPMailer();
    $autoRespond->isSMTP();
    $autoRespond->CharSet = 'UTF-8';
    $autoRespond->SMTPAuth = true;
    $autoRespond->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $autoRespond->Port = SMTP_PORT;
    $autoRespond->Username = SMTP_USER; // Use the same SMTP credentials
    $autoRespond->Password = SMTP_PASS;
    $autoRespond->Host = SMTP_HOST; 

    $autoRespond->setFrom('info@soilwrap.com', 'Soilwrap Technologies');
    $autoRespond->addAddress($email);
    $autoRespond->Subject = 'Confirmation of Your Contact Request';
    $autoRespond->isHTML(true); // Set the email format to HTML
    
    // Load and parse the email template
    $templatePath = 'email-template.html'; // Updated path to email template
    if (!file_exists($templatePath)) {
        throw new Exception('Email template not found');
    }
    
    $template = file_get_contents($templatePath);

    // Replace placeholders with actual values
    $autoRespondBody = str_replace(
        ['{{name}}', '{{subject}}'],
        [$name, $subject],
        $template
    );
    $autoRespond->Body = $autoRespondBody;

    // Send the auto-response email
    $autoRespond->send();
    
    // Redirect or send success response
    header('Location: ../../thankyou'); // Adjusted to navigate correctly
    exit;

} catch (Exception $e) {
    // Log the error message and show a user-friendly message
    error_log("Mailer Error: {$mail->ErrorInfo}");
    echo "Sorry, there was an issue sending your message. Please try again later.";
}
