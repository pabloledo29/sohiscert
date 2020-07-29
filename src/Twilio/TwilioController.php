<?php

namespace App\Twilio;

use App\Entity\Operator;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
class TwilioController extends AbstractController
{

    public function __construct($container)
    {
        $this->container = $container;
    }
    /**
     * @Route("/private/twilio/sms/send", name="private_twilio_sms_send")
     * Envío de un SMS para un operador
     */
    public function privateTwilioSmssend(Array $data)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $twilio_sid =$this->container->getParameter('twilio_sid');
        $twilio_token = $this->container->getParameter('twilio_token');
        $twilio_number = $this->container->getParameter('twilio_number');
        $twilio_acconts_sid = $this->container->getParameter('twilio_acconts_sid');
        
        $twilio = new Client($twilio_sid, $twilio_token,$twilio_acconts_sid);
      
        
   
        $to_number ="+34". $this->getDoctrine()->getManager()->getRepository(Operator::class)->getOperatorTelefono($data['opNop'])[0]['opTel'];         
        $body = $data['description'];

        $message = $twilio->messages
                        ->create($to_number, // to
                                [
                                    "body" => $body,
                                    "from" => $twilio_number
                                ]
                        );

        print($message->sid);

    }

    /**
     * @Route("/admin/twilio/sms/send", name="admin_twilio_sms_send")
     * Envío de un SMS para un operador
     */
    public function adminTwilioSmssend(Array $data)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $twilio_sid =$this->container->getParameter('twilio_sid');
        $twilio_token = $this->container->getParameter('twilio_token');
        $twilio_number = $this->container->getParameter('twilio_number');
        $twilio_acconts_sid = $this->container->getParameter('twilio_acconts_sid');
        
        $twilio = new Client($twilio_sid, $twilio_token,$twilio_acconts_sid);
      
        
   
        $to_number ="+34". $this->getDoctrine()->getManager()->getRepository(Operator::class)->getOperatorTelefono($data['opNop'])[0]['opTel'];         
        $body = $data['description'];

        $message = $twilio->messages
                        ->create($to_number, // to
                                [
                                    "body" => $body,
                                    "from" => $twilio_number
                                ]
                        );

        print($message->sid);

    }


    /**
     * @Route("/private/twilio/whatsapp/send", name="private_twilio_whatsapp_send")
     * Envío de un Whatsapp para un operador
     */
    public function privateTwilioWhatsappsSend(Array $data)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $twilio_sid = $this->container->getParameter('twilio_sid');
        $twilio_token = $this->container->getParameter('twilio_token');
        $twilio_number = $this->container->getParameter('twilio_whatsapp_number');
        $twilio_acconts_sid = $this->container->getParameter('twilio_acconts_sid');
        $twilio = new Client($twilio_sid, $twilio_token,$twilio_acconts_sid);

        $to_number ="+34". $this->getDoctrine()->getManager()->getRepository(Operator::class)->getOperatorTelefono($data['opNop'])[0]['opTel']; 

        $body = $data["description"];
        $message = $twilio->messages
                        ->create("whatsapp:$to_number", // to
                                [
                                    "from" => "whatsapp:$twilio_number",
                                    "body" => $body
                                ]
                        );

        print($message->sid);
    }

    /**
     * @Route("/admin/twilio/whatsapp/send", name="admin_twilio_whatsapp_send")
     * Envío de un Whatsapp para un operador
     */
    public function adminTwilioWhatsappsSend(Array $data)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $twilio_sid = $this->container->getParameter('twilio_sid');
        $twilio_token = $this->container->getParameter('twilio_token');
        $twilio_number = $this->container->getParameter('twilio_whatsapp_number');
        $twilio_acconts_sid = $this->container->getParameter('twilio_acconts_sid');
        
        $twilio = new Client($twilio_sid, $twilio_token,$twilio_acconts_sid);

        $to_number ="+34". $this->getDoctrine()->getManager()->getRepository(Operator::class)->getOperatorTelefono($data['opNop'])[0]['opTel']; 

        $body = $data["description"];
        $message = $twilio->messages
                        ->create("whatsapp:$to_number", // to
                                [
                                    "from" => "whatsapp:$twilio_number",
                                    "body" => $body
                                ]
                        );

        print($message->sid);
    }
            
}