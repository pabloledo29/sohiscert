<?php

namespace App\MessageBird;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Surfnet\MessageBirdApiClient\Messaging\Message;
class MessageBirdController extends AbstractController
{
    /**
     * @Route("/private/messagebird/whatsapp/send", name="private_messagebird_whatsapp_send")
     * Envío de un mensaje de Whatsapp para un operador
     */
    public function privateWhatsappSend(){
        $message = new Message(
            'SURFnet',
            '31612345678',
            'Your one-time SMS security token: 9832'
        );
        
        /** @var \Surfnet\MessageBirdApiClientBundle\Service\MessagingService $messaging */
        $messaging = $this->get('surfnet_message_bird_api_client.messaging');
        $result = $messaging->send($message);
        
        if ($result->isSuccess()) {
            // Message has been buffered, sent or delivered.
        }
    }

    /**
     * @Route("/admin/messagebird/whatsapp/send", name="admin_messagebird_whatsapp_send")
     * Envío de un mensaje de Whatsapp para un administrador
     */
    public function adminWhatsappSend(){
        $message = new Message(
            'SURFnet',
            '31612345678',
            'Your one-time SMS security token: 9832'
        );
        
        /** @var \Surfnet\MessageBirdApiClientBundle\Service\MessagingService $messaging */
        $messaging = $this->get('surfnet_message_bird_api_client.messaging');
        $result = $messaging->send($message);
        
        if ($result->isSuccess()) {
            // Message has been buffered, sent or delivered.
        }
    }

}