<?php

require_once __DIR__ . '/../utils/EnvUtil.php';
require_once __DIR__ . '/../services/EmailService.php';

class EmailController {
    public function __construct() {
    }

    public function handleRequest() {
        $version = $_POST['version'] ?? 1;
        $emailService = new EmailService();
        $recipientEmail = $_SESSION['form_data']['email'];
        $deceasedPerson = $_SESSION['form_data']['deceasedPersonName'] ?? 'your beloved one';
        $messageType = ucwords($_SESSION['form_data']['messageType'] ?? 'Content');
        $content = $_SESSION['contents'][$version] ?? 'No content found';

        // Create HTML email template
        $htmlContent = $this->createEmailTemplate($messageType, $version, $content, $deceasedPerson);
        
        // Create plain text version as fallback
        $plainContent = strip_tags($htmlContent);

        $emailService->sendEmail(
            $recipientEmail, 
            "{$messageType}: Your Personalized Tribute for {$deceasedPerson}", 
            $htmlContent,
            $plainContent
        );

        echo json_encode(['success' => true]);
    }

    public function sendContactFormEmail($name, $email, $company, $phone, $message) {
        $emailService = new EmailService();
        
        // Create a simple HTML email for contact form
        $htmlContent = $this->createSimpleContactEmail($name, $email, $company, $phone, $message);
        
        // Create plain text version
        $plainContent = "New contact form submission from: $name\n\n"
                      . "Email: $email\n"
                      . "Company: $company\n"
                      . "Phone: $phone\n\n"
                      . "Message:\n$message\n\n"
                      . "---\n"
                      . "Sent from EverRemember contact form";
        
        // Send to admin
        $adminEmail = EnvUtil::getEnv('GMAIL_SMTP_USER', 'md.masum.work@gmail.com');
        
        // Pass sender's email as replyTo parameter
        $emailService->sendEmail(
            $adminEmail, 
            "EverRemember Contact: $name", 
            $htmlContent,
            $plainContent,
            $email // Set reply-to as the sender's email
        );
        
        return true;
    }

    private function createSimpleContactEmail($name, $email, $company, $phone, $message) {
        // Format message with proper line breaks but keep it simple
        $message = nl2br(htmlspecialchars($message));
        
        return <<<HTML
        <div style="font-family: Arial, sans-serif; line-height: 1.6;">
            <p>
                {$message}
            </p>
            <p><strong>Sender Details:</strong></p>
            <p>
                {$name} <br />
                {$email} <br />
                {$phone} <br />
                {$company}                
            </p>
            <p style="color: #777; font-size: 12px; margin-top: 30px;">
                --- <br>
                Sent from EverRemember contact form
            </p>
        </div>
        HTML;
    }

    private function createEmailTemplate($messageType, $version, $content, $deceasedPerson) {
        $baseUrl = EnvUtil::getEnv('BASE_URL');
        
        // Replace double new lines with two <br> tags and single new lines with <br> tags
        $content = str_replace("\n\n", "<br><br>", $content);
        $content = str_replace("\n", "<br>", $content);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                .header {
                    background-color: #f9f9f9;
                    padding: 20px;
                    position: relative;
                }
                .header h2 {
                    color: #444;
                    margin-bottom: 10px;
                }
                .header p {
                    color: #666;
                    font-size: 16px;
                    margin: 0;
                }
                .footer {
                    margin-top: 45px;
                    border-top: 1px solid #eee;
                    font-size: 14px;
                    color: #666;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="border: 1px solid #eee;">
                <div class="header">
                    <h2>Your {$messageType} for {$deceasedPerson}</h2>
                    <p>Version {$version}</p>
                </div>

                <div style="background-color: #fff; padding: 20px; font-size: 14px;">
                    <p>{$content}</p>
                </div>
            </div>
            <div class="footer">
                <p>Generated with care by <a href="{$baseUrl}">EverRemember</a></p>
            </div>
        </body>
        </html>
        HTML;
    }
}
