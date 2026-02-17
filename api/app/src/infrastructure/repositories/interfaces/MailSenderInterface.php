<?php

namespace infrastructure\repositories\interfaces;

interface MailSenderInterface {
    public function send(string $to, string $subject, string $htmlBody) : void;
}