<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../utils/DebugUtil.php';
require_once __DIR__ . '/../utils/EnvUtil.php';

class EmailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }

    private function setupSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = EnvUtil::getEnv('SMTP_SERVER');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = EnvUtil::getEnv('SMTP_USER');
        $this->mail->Password = EnvUtil::getEnv('SMTP_PASS');
        $this->mail->SMTPSecure = EnvUtil::getEnv('SMTP_SECURE');
        $this->mail->Port = EnvUtil::getEnv('SMTP_PORT');
    }

    public function sendEmail($to, $subject, $htmlBody, $plainBody, $replyTo = null) {
        try {
            $this->mail->setFrom(EnvUtil::getEnv('SEND_FROM_EMAIL'), 'EverRemember');
            $this->mail->addAddress($to);
            
            // Add reply-to address if provided
            if ($replyTo) {
                $this->mail->addReplyTo($replyTo);
            }
            
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
