<?php

require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

class EmailController {
    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
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

    private function createEmailTemplate($messageType, $version, $content, $deceasedPerson) {
        $domainName = $_ENV['DOMAIN_NAME'];
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
                    <p>Version {$version} masum</p>
                </div>

                <div style="background-color: #fff; padding: 20px; font-size: 14px;">
                    {$content}
                </div>
            </div>
            <div class="footer">
                <p>Generated with care by <a href="{$domainName}">EverRemember</a></p>
            </div>
        </body>
        </html>
        HTML;
    }
}
