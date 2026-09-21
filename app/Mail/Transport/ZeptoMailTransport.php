<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class ZeptoMailTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email_address' => [
                    'address' => $address->getAddress(),
                    'name'    => $address->getName() ?: '',
                ],
            ];
        }

        $from = $email->getFrom()[0];

        $payload = array_filter([
            'from' => [
                'address' => $from->getAddress(),
                'name'    => $from->getName() ?: '',
            ],
            'to'       => $to,
            'subject'  => $email->getSubject(),
            'htmlbody' => $email->getHtmlBody(),
            'textbody' => $email->getTextBody(),
        ]);

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => $this->apiKey,
        ])->post('https://api.zeptomail.in/v1.1/email', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'ZeptoMail API error (' . $response->status() . '): ' . $response->body()
            );
        }
    }

    public function __toString(): string
    {
        return 'zepto';
    }
}
