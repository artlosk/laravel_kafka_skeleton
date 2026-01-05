<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

class FileTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $originalMessage = $message->getOriginalMessage();

        // Файлы сохраняются в storage/app/private/emails/
        $filename = 'emails/' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.html';

        $content = $this->formatEmailContent($originalMessage);

        Storage::disk('local')->put('private/' . $filename, $content);

        $toAddresses = [];
        if ($originalMessage instanceof Email) {
            foreach ($originalMessage->getTo() as $address) {
                $toAddresses[] = $address->getAddress();
            }
        }

        Log::info('Email saved to file', [
            'filename' => 'private/' . $filename,
            'to' => $toAddresses,
            'subject' => $originalMessage instanceof Email ? $originalMessage->getSubject() : 'N/A',
        ]);
    }

    protected function formatEmailContent(RawMessage $message): string
    {
        $html = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='UTF-8'>\n<title>Email</title>\n</head>\n<body>\n";
        $html .= "<div style='background: #f5f5f5; padding: 20px; margin-bottom: 20px;'>\n";
        $html .= "<h2>Email Details</h2>\n";

        if ($message instanceof Email) {
            $html .= "<p><strong>From:</strong> " . $this->formatAddresses($message->getFrom()) . "</p>\n";
            $html .= "<p><strong>To:</strong> " . $this->formatAddresses($message->getTo()) . "</p>\n";
            $html .= "<p><strong>Subject:</strong> " . htmlspecialchars($message->getSubject() ?? '') . "</p>\n";
            $html .= "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
            $html .= "</div>\n";
            $html .= "<div style='padding: 20px; background: white;'>\n";
            $htmlBody = $message->getHtmlBody();
            $textBody = $message->getTextBody();
            $html .= $htmlBody ?: ($textBody ? nl2br(htmlspecialchars($textBody)) : '');
        } else {
            // Если это RawMessage, просто сохраняем его как есть
            $html .= "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>\n";
            $html .= "</div>\n";
            $html .= "<div style='padding: 20px; background: white;'>\n";
            $html .= "<pre>" . htmlspecialchars($message->toString()) . "</pre>";
        }

        $html .= "</div>\n";
        $html .= "</body>\n</html>";

        return $html;
    }

    protected function formatAddresses(array $addresses): string
    {
        $formatted = [];
        foreach ($addresses as $address) {
            $formatted[] = $address->getName()
                ? $address->getName() . ' <' . $address->getAddress() . '>'
                : $address->getAddress();
        }
        return implode(', ', $formatted);
    }

    public function __toString(): string
    {
        return 'file';
    }
}

