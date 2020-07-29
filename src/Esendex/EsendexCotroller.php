<?php

namespace App\Esendex;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Esendex\Model\DispatchMessage;
use Esendex\Model\Message;
use Esendex\DispatchService;
use Esendex\Authentication\LoginAuthentication;
use Esendex\InboxService;
use Esendex\MessageHeaderService;
use Esendex\MessageBodyService;
class EsendexCotroller extends AbstractController
{
    /**
     * @Route("/private/esendex/sms/send", name="private_esendex_sms_send")
     * Envío de un SMS para un operador
     */
    public function privateSendSms()
    {
        $message = new DispatchMessage(
            "WebApp", // Send from
            "01234567890", // Send to any valid number
            "My Web App is SMS enabled!",
            Message::SmsType
        );
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex Account Reference
            "user@example.com", // Your login email address
            "password" // Your password
        );
        $service = new DispatchService($authentication);
        $result = $service->send($message);
        
        print $result->id();
        print $result->uri();
    }

    /**
     * @Route("/admin/esendex/sms/send", name="admin_esendex_sms_send")
     * Envío de un SMS para un administrador
     */
    public function adminSendSms()
    {
        $message = new DispatchMessage(
            "WebApp", // Send from
            "01234567890", // Send to any valid number
            "My Web App is SMS enabled!",
            Message::SmsType
        );
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex Account Reference
            "user@example.com", // Your login email address
            "password" // Your password
        );
        $service = new DispatchService($authentication);
        $result = $service->send($message);
        
        print $result->id();
        print $result->uri();
    }

    /**
     * @Route("/private/esendex/sms/retriving", name="private_esendex_sms_retriving")
     * Simula un buzón de entrada de SMSes para un operador
     * 
     */
    public function privateRetrivingSms()
    {
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex Account Reference
            "user@example.com", // Your login email address
            "password" // Your password
        );
        $service = new InboxService($authentication);
    
        $result = $service->latest();
    
        print "Total Inbox Messages: {$result->totalCount()}";
        print "Fetched: {$result->count()}";
        foreach ($result as $message) {
            print "Message from: {$message->originator()}, {$message->summary()}";
        }
    }

    /**
     * @Route("/admin/esendex/sms/retriving", name="admin_esendex_sms_retriving")
     * Simula un buzón de entrada de SMSes para un administrador
     */
    public function adminRetrivingSmses()
    {
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex Account Reference
            "user@example.com", // Your login email address
            "password" // Your password
        );
        $service = new InboxService($authentication);
    
        $result = $service->latest();
    
        print "Total Inbox Messages: {$result->totalCount()}";
        print "Fetched: {$result->count()}";
        foreach ($result as $message) {
            print "Message from: {$message->originator()}, {$message->summary()}";
        }
    }
    /**
     * @Route("/private/esendex/sms/track", name="private_esendex_sms_track")
     * Rastrea el estado de un SMS para un operador
     */
    public function privateTrackSms()
    {
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex account reference
            "user@example.com", // Your login email
            "password" // Your password
        );
        $headerService = new MessageHeaderService($authentication);
        $message = $headerService->message("messageId");
        print_r($message->status());
    }

    /**
     * @Route("/admin/esendex/sms/track", name="admin_esendex_sms_track")
     * Rastrea el estado de un SMS para un administrador
     */
    public function adminTrackSms()
    {
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex account reference
            "user@example.com", // Your login email
            "password" // Your password
        );
        $headerService = new MessageHeaderService($authentication);
        $message = $headerService->message("messageId");
        print_r($message->status());
    }
    /**
     * @Route("/private/esendex/sms/retriving_full", name="private_esendex_sms_retriving_full")
     * Trae todo el cuerpo de un mensaje para un operador
     */
    public function privateRetrivingFullMessage()
    {
        $messageId = "unique-id-of-message";
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex Account Reference
            "user@example.com", // Your login email address
            "password" // Your password
        );
        $service = new MessageBodyService($authentication);

        $result = $service->getMessageBodyById($messageId);

        print $result;
    }

    /**
     * @Route("/admin/esendex/sms/retriving_full", name="admin_esendex_sms_retriving_full")
     * Trae todo el cuerpo de un mensaje para un administrador
     */
    public function adminRetrivingFullMessage()
    {
        $messageId = "unique-id-of-message";
        $authentication = new LoginAuthentication(
            "EX000000", // Your Esendex Account Reference
            "user@example.com", // Your login email address
            "password" // Your password
        );
        $service = new MessageBodyService($authentication);

        $result = $service->getMessageBodyById($messageId);

        print $result;
    }
}
