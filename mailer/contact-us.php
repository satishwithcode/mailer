<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'smtp/src/Exception.php';
require 'smtp/src/PHPMailer.php';
require 'smtp/src/SMTP.php';

// Sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Collect and sanitize form data
$name = sanitize_input($_POST['name']);
$email = sanitize_input($_POST['email']);
$contact = sanitize_input($_POST['mobile']);
$service = sanitize_input($_POST['service']);
$message = sanitize_input($_POST['message']);

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@ansumiti.com';
    $mail->Password = 'Unitech#123%';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('info@ansumiti.com', 'Lead');
    $mail->addAddress('ansumiti89@gmail.com', 'Ansumiti Technosoft Pvt Ltd.');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Us Form Submission';
    $mail->Body    = "<p><strong>Name:</strong> $name</p>" .
                     "<p><strong>Email:</strong> $email</p>" .
                     "<p><strong>Contact Number:</strong> $contact</p>" .
                     "<p><strong>Service Interested In:</strong> $service</p>" .
                     "<p><strong>Message:</strong> $message</p>";

    // Send the email
    $mail->send();
    echo 'Message has been sent';

    // Auto-response email to the user
    $autoRespond = new PHPMailer(true);
    $autoRespond->isSMTP();
    $autoRespond->Host = 'smtp.hostinger.com';
    $autoRespond->SMTPAuth = true;
    $autoRespond->Username = 'info@ansumiti.com';
    $autoRespond->Password = 'Unitech#123%';
    $autoRespond->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $autoRespond->Port = 587;

    $autoRespond->setFrom('info@ansumiti.com', 'Ansumiti Technosoft Pvt Ltd.');
    $autoRespond->addAddress($email); // Send auto-response to the user who filled out the form
    $autoRespond->isHTML(true);
    $autoRespond->Subject = 'Confirmation of Your Contact Request';
    $autoRespond->Body    = "<p>Dear $name,</p>" .
                            "<p>Thank you for contacting us regarding <strong>$service</strong>.</p>" .
                            "<p>We have received your message and will get back to you shortly.</p>" .
                            "<p>Best Regards, <br>Ansumiti Technosoft Pvt Ltd.</p>";

    // Send the auto-response email
    $autoRespond->send();

    // Redirect to a thank you or success page
    header('Location: ../../thankyou.php'); // Replace with your thank you page URL
    exit;

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
