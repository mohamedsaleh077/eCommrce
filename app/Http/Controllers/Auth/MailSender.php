<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Cache;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;
use Valorin\Random\Random;

class MailSender
{
    static public function sendMail($subject, $body, $email = null)
    {
        $email = (new MailtrapEmail())
        ->from(new Address('hello@demomailtrap.co', 'Mailtrap Test'))
        ->to(new Address('mohammed.saleh.22707@gmail.com'))
        ->subject("eCommerce - {$subject}")
        ->category('Integration Test')
        ->text($body);

        $response = MailtrapClient::initSendingEmails(
            apiKey: $_ENV['MAILTRAP_API_TOKEN']
        )->send($email);

        return ResponseHelper::toArray($response);
    }

    static public function setVerifyCode($user_id)
    {
        $otp = Random::otp(6);
        Cache::put("email_{$user_id}", $otp, 60*3);

        return $otp;
    }

    static public function checkVerifyCode($code, $user_id)
    {
        $otp = Cache::get("email_{$user_id}");
        return $otp == $code;
    }

    static public function sendVerificationCode($userId)
    {
        $otp = MailSender::setVerifyCode($userId);
        MailSender::sendMail("Account Verification", "Your verification code is: {$otp}");
    }
}