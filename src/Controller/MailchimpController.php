<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserOperator;
use App\Mailchimp\Provider\DoctrineListProvider;
use App\Mailchimp\Provider\MailchimpDynamicProvider;
use App\Repository\UserRepository;
use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class MailchimpController extends AbstractController
{
    /**
     * Añadir campaña a Mailchimp para poder enviar mensajes a las personas de la campaña
     */
    public function añadirCampaña(Array $data, String $group_option, String $apikey, String $email_from_address, String $email_from_name, Array $subscriberList) {
    
        $audiences = ['8477d64f6c'];
        
        if($group_option){
          $asunto = $data['asunto'];
          $description = $data['description'];
        $email_from_address = 'ignacio.fernandez@atlantic.es';
          $email_from_name = 'J Ignacio Fernández Seda';

          $template_mailchimp = 132696;
          $json_create = [
            'type' => 'regular',
            'recipients' => [
              'list_id' => $audiences[0]
              /*'segment_opts' => [
                'match' => 'all',
                'conditions' => [
                  'condition_type' => 'TextMerge',
                  'op'=> 'is',
                  'field'=> 'EMAIL',
                  'value'=> 'EMAIL'
                ]
              ]*/
            ],
            'settings' => [
              'preview_text' => $description,
              'subject_line' => $asunto,
              'title' => $asunto,
              'from_name' => $email_from_name,
              'reply_to' => $email_from_address,
              'template_id' => $template_mailchimp
            ]
          ];
        }
          $curlhttpclient =  new CurlHttpClient();
          $route = 'https://us19.api.mailchimp.com/3.0/campaigns';
          try {
            $CURLresponse = $curlhttpclient->request('POST', $route, [
              'auth_basic' => 'ignacio.fernandez@atlantic.es:'.$apikey,
              'headers' => ['content-type: application/json'],
              'json' => $json_create,
            ]);
          } catch (Error $e) {
            var_dump($e->getMessage());
           exit;
          }
       
         return $CURLresponse->getContent();

    }


    /**
     * @Route("/private/campaing_send", name="private_campaing_send")
     * Enviar mensajes de campaña, este es el único metodo que debe de ser llamado desde fuera
     */
    public function privateEnviarMensajesDeCampaña(array $data, String $group_option = 'all'){
      
      if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
        throw $this->createAccessDeniedException();
    }
      $apikey=$this->container->getParameter('mailchimp_api_key');
      $email_from_address=$this->container->getParameter('email_from_address');
      $email_from_name=$this->container->getParameter('email_from_name');
 
      $userRepository = $this->getDoctrine()->getManager()->getRepository(UserOperator::class);
      $mailchimpDynamicProvider = new MailchimpDynamicProvider($userRepository);
      $subscriberList = (new DoctrineListProvider($this->getDoctrine()->getManager(), UserOperator::class, $mailchimpDynamicProvider))->getLists();
      $curl_response = null;
      
      if(strcmp($group_option, 'all') == 0){ 
        $camp =  $this->añadirCampaña($data, $group_option,$apikey, $email_from_address, $email_from_name,$subscriberList);
        
        $id = json_decode($camp)->id;
        $json_create = [
          'campaign_id' => $id
        ];
        
          $curlhttpclient =  new CurlHttpClient();
          $route = 'https://us19.api.mailchimp.com/3.0/campaigns/'. $id .'/actions/send';
          try {
            $CURLresponse = $curlhttpclient->request('POST', $route, [
              'auth_basic' => 'ignacio.fernandez@atlantic.es:bccbc65594b97af78b8d502147d008b6-us19',
              'headers' => ['content-type: application/json'],
              'json' => $json_create,
            ]);
          } catch (Error $e) {
            var_dump($e);
            exit;
          }

         $curl_response = $CURLresponse->getContent();
      
      }


        
      return $curl_response;
    }

    /**
     * @Route("/admin/campaing_send", name="admin_campaing_send")
     * Enviar mensajes de campaña, este es el único metodo que debe de ser llamado desde fuera
     */
    public function adminEnviarMensajesDeCampaña(array $data, String $group_option = 'all'){
      if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException();
      }
      $apikey=$this->container->getParameter('mailchimp_api_key');
      $email_from_address=$this->container->getParameter('email_from_address');
      $email_from_name=$this->container->getParameter('email_from_name');
      
      $userRepository = $this->getDoctrine()->getManager()->getRepository(UserOperator::class);
      $mailchimpDynamicProvider = new MailchimpDynamicProvider($userRepository);
      $subscriberList = (new DoctrineListProvider($this->getDoctrine()->getManager(), UserOperator::class, $mailchimpDynamicProvider))->getLists();
      $curl_response = null;
      if(strcmp($group_option, 'all') == 0){ 
        $camp =  $this->añadirCampaña($data, $group_option,$apikey, $email_from_address, $email_from_name,$subscriberList);
        
        $id = json_decode($camp)->id;
        $json_create = [
          'campaign_id' => $id
        ];
        
          $curlhttpclient =  new CurlHttpClient();
          $route = 'https://us19.api.mailchimp.com/3.0/campaigns/'. $id .'/actions/send';
          try {
            $CURLresponse = $curlhttpclient->request('POST', $route, [
              'auth_basic' => 'ignacio.fernandez@atlantic.es:bccbc65594b97af78b8d502147d008b6-us19',
              'headers' => ['content-type: application/json'],
              'json' => $json_create,
            ]);
          } catch (Error $e) {
            var_dump($e);
            exit;
          }

         $curl_response = $CURLresponse->getContent();
      
      }


        
      return $curl_response;
    }


      /**
     * @Route("/admin/campaigns", name="read_campaings")
     */
 /*   public function verCampañas(Request $request){
        $curlhttpclient =  new CurlHttpClient();
        $route = 'https://us19.api.mailchimp.com/3.0/campaigns';
        try {
          $CURLresponse = $curlhttpclient->request('GET', $route, [
            'auth_basic' => 'ignacio.fernandez@atlantic.es:bccbc65594b97af78b8d502147d008b6-us19',
           
          ]);
        } catch (Exception $e) {
          var_dump($e);
          exit;
        }
        $response = new Response();
        $response->setContent(json_encode([
          $CURLresponse->getContent(),
         ]));
        $response->headers->set('Content-Type', 'application/json');
      return $response;
    }*/


    /**
     * Borrar campaña una vez enviado los mensajes (Esto es porque cada usuario va crear una campaña)
     */
    public function deleteCampaing($id){
      $json_create = [
        'campaign_id' => $id
      ];

        $curlhttpclient =  new CurlHttpClient();
        $route = 'https://us19.api.mailchimp.com/3.0/campaigns/'.$id;
        try {
          $CURLresponse = $curlhttpclient->request('DELETE', $route, [
            'auth_basic' => 'ignacio.fernandez@atlantic.es:bccbc65594b97af78b8d502147d008b6-us19',
            'headers' => ['content-type: application/json'],
            'json' => $json_create,
          ]);
        } catch (Error $e) {
          var_dump($e);
          exit;
        }
        return $id;
    }
}
