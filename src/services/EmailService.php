<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../utils/DebugUtil.php';

class EmailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }

    private function setupSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['GMAIL_SMTP_SERVER'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['GMAIL_SMTP_USER'];
        $this->mail->Password = $_ENV['GMAIL_SMTP_PASS'];
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = $_ENV['GMAIL_SMTP_PORT'];
    }

    public function sendEmail($to, $subject, $htmlBody, $plainBody) {
        try {
            $this->mail->setFrom($_ENV['GMAIL_SMTP_USER'], 'EverRemember');
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $htmlBody;
            $this->mail->AltBody = $plainBody; // Plain text version
            $this->mail->CharSet = 'UTF-8';
            $this->mail->send();
        } catch (Exception $e) {
            throw new Exception('Email could not be sent. Mailer Error: ' . $this->mail->ErrorInfo);
        }
    }
}
