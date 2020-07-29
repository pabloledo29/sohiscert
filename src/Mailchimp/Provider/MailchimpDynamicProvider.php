<?php

namespace App\Mailchimp\Provider;

use Welp\MailchimpBundle\Provider\DynamicProviderInterface;
use Welp\MailchimpBundle\Subscriber\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserOperatorRepository;
use App\Entity\UserOperator;
use DateTime;
use DrewM\MailChimp\MailChimp;
use Welp\MailchimpBundle\Event\SubscriberEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Welp\MailchimpBundle\Subscriber\ListSynchronizer;
use  Welp\MailchimpBundle\Subscriber\ListRepository;

class MailchimpDynamicProvider implements DynamicProviderInterface
{
    const TAG_NOMBRE = 'NOMBRE';
    const TAG_CIF = 'CIF';
    const TAG_NOP = 'NOP';
    const TAG_TIPO = 'TIPO';
    const TAG_FREGISTRO = 'FREGISTRO';
    //const TAG_GRUPO = "REGISTRO";

    protected $userRepository;
    protected $listId;
    public function __construct(UserOperatorRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->mailchimp = new MailChimp('bccbc65594b97af78b8d502147d008b6-us19'); //API-KEY
        $this->listRepo = new ListRepository($this->mailchimp);
        $this->cont = 0;
        $this->borrar = 0;
        $this->listIds = ['8477d64f6c' => 3,'noexiste' =>0]; //LISTAS definidas en Maichimp con el límite de elementos de cada una 2000
    }
    public function setListId(string $listId)
    {

        $this->listId = $listId;
    }

    //Obtiene los suscriptores desde symfony, y los introduce en Maichimp, ten en cuenta que el email debe de ser distinto o no funcionará
    public function getSubscribers()
    {
        $users = $this->userRepository->findUserSubscriber();
        
        $cont_inicio = 0;
        $subscribers = [];
        $this->cont = 0;
        foreach ($this->listIds as $keylistId => $valueLimitSubscriber) {
            
    
            if (sizeof($subscribers) <= $valueLimitSubscriber) {
               
                $users = array_slice($users,$cont_inicio, ($cont_inicio+1)*$valueLimitSubscriber);
               
                $this->setListId($keylistId);
            
                array_push($subscribers,array_map(function (UserOperator $user) {
                    $subscriber = null;

                    $operators = $user->getOperators();
                    $groups = ['1','IFS','3','1OP','2OP','4'];
                    
                    $expendientes = [];
                    $tipos = [];
                    if ($operators) {
                        foreach ($operators as $operator) {
                            if ($user->getEmail()) {
                                $groups[]= $operator->getOpReg();
                                $expendientes []=$operator->getOpNop();
                                $tipos []=$operator->getOpSreg();
                                $subscriber = new Subscriber($user->getEmail(), [
                                    self::TAG_NOMBRE => $operator->getOpTecDeno(),
                                    self::TAG_CIF => $user->getUsername(),
                                    self::TAG_NOP => implode("-",$expendientes),
                                    self::TAG_TIPO => implode("-",$tipos),
                                    self::TAG_FREGISTRO => date_format($user->getCreatedDate(), 'd-m-Y'),
                                    //self::TAG_GRUPO => implode(",",$groups)
                                ], [
                                    'language'   => 'es_ES',
                                    'email_type' => 'html'
                                ]);
                                $dispatcher = new EventDispatcher();

                                $dispatcher->dispatch(new SubscriberEvent($this->listId, $subscriber), SubscriberEvent::EVENT_SUBSCRIBE);
                                $this->borrar +=1;
                            }
                        }
                    }


                    if ($subscriber && $this->cont<=$this->listIds[$this->listId]) {
                       $this->listRepo->subscribe($this->listId, $subscriber);
                        return $subscriber;
                    }
                }, $users));
            }
            $cont_inicio= $cont_inicio+1;
        }
        
        return $subscribers;
    }


      public function getSubscriber($listId)
    {
        $users = $this->userRepository->findUserSubscriber();
        $this->setListId($listId);
        $this->cont = 0;
        $subscribers = array_map(function(UserOperator $user) {
            $subscriber = null;
            $operators = $user->getOperators();
            if($operators){
                foreach($operators as $operator){
                    if($user->getEmail()){
                        
                    
                        $subscriber = new Subscriber($user->getEmail(), [
                            self::TAG_NOMBRE => $operator->getOpTecDeno(),
                            self::TAG_CIF => $user->getUsername(),
                            self::TAG_NOP => $operator->getOpNop(),
                            self::TAG_TIPO => $operator->getOpSreg(),
                            self::TAG_FREGISTRO => date_format($user->getCreatedDate(), 'd-m-Y')
                        ],[
                            'language'   => 'es_ES',
                            'email_type' => 'html'
                        ]);
                        $dispatcher = new EventDispatcher();

                        $dispatcher->dispatch(new SubscriberEvent($this->listId, $subscriber), SubscriberEvent::EVENT_SUBSCRIBE);
                       
                    }
                }
                
            }
            

            if ($subscriber && $this->cont<=$this->lisIds[$this->listId]) {
                $this->cont += 1;
                $this->listRepo->subscribe($this->listId, $subscriber);
                 return $subscriber;
             }
            
        }, $users);
       
        return $subscribers;
    }
}
