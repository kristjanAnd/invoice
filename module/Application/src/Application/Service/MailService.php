<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 25.02.15
 * Time: 18:29
 */

namespace Application\Service;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\Stdlib\Parameters;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class MailService extends AbstractService {

    public function send(SmtpTransport $transport = null, Message $message, array $attachments = array ()) {

        if ($transport == null) {
            $transport = new Sendmail();
        }
        $content = $message->getBody();
        $parts = $attachments;

        $parts = array ();

        $bodyMessage = new \Zend\Mime\Message();
        $multiPartContentMessage = new \Zend\Mime\Message();

        $text = new Part(html_entity_decode(strip_tags(str_replace("<br />", "\n", $content))));
        $text->type = "text/plain";
        $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $text->charset = 'UTF-8';
        $multiPartContentMessage->addPart($text);

        $html = new Part($content);
        $html->type = Mime::TYPE_HTML;
        $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $html->charset = 'utf-8';
        $multiPartContentMessage->addPart($html);


        $multiPartContentMimePart = new Part($multiPartContentMessage->generateMessage());
        $multiPartContentMimePart->type = 'multipart/alternative;' . PHP_EOL . ' boundary="' .
            $multiPartContentMessage->getMime()->boundary() . '"';

        $bodyMessage->addPart($multiPartContentMimePart);

        foreach ($attachments as $attachment) {
            $bodyMessage->addPart($attachment);
        }

        $message->setBody($bodyMessage);
        $message->setEncoding("UTF-8");

        $transport->send($message);

    }

    public function getAttachment($path, $name) {
        $part = new Part(file_get_contents($path));
        $part->type = 'application/pdf';
        $part->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
        $part->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
        $part->charset = 'UTF-8';
        $part->filename = basename($name);

        return $part;
    }

    public function getTransport() {
        $config = $this->locator->get('Config');
        if(!isset($config['mail']['smtpOptions'])){
            return null;
        }
        $transport = new SmtpTransport();
        $options   = new SmtpOptions($config['mail']['smtpOptions']);
        $transport->setOptions($options);

        return $transport;
    }
} 