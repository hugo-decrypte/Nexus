<?php

namespace infrastructure\repositories;

use application_core\exceptions\SendMailException;
use infrastructure\repositories\interfaces\MailSenderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailSender implements MailSenderInterface {
    private string $host = 'mailer';
    private int $port = 1025;

    public function send(string $to, string $subject, string $htmlBody) : void {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->Port       = $this->port;
            $mail->SMTPAuth   = false;
            $mail->SMTPAutoTLS = false;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('noreply@projet-tutore-nexus.com', 'Nexus');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->send();
        } catch (Exception $e) {
            throw new SendMailException();
        }
    }
}