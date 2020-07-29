<?php

namespace App\Mailchimp\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Welp\MailchimpBundle\Provider\ListProviderInterface;
use Welp\MailchimpBundle\Provider\DynamicProviderInterface;
use App\Entity\UserOperator;
use Welp\MailchimpBundle\Subscriber\ListSynchronizer;
use Welp\MailchimpBundle\Subscriber\SubscriberList;

class DoctrineListProvider implements ListProviderInterface
{

    private $em;
    private $listEntity;
    private $subscriberProvider;

    public function __construct(EntityManagerInterface $entityManager, $listEntity,MailchimpDynamicProvider  $subscriberProvider)
    {
        $this->em = $entityManager;
        $this->listEntity = $listEntity;
        $this->subscriberProvider = $subscriberProvider;
    }


    public function getListId($listId)
    {
        return $listId;
    }
    /**
     * {@inheritdoc}
     */
    public function getList($listId)
    {
        $subscriberList  = new SubscriberList('lista1',$this->subscriberProvider);
        $subscriberList->setWebhookUrl("http://us19.admin.mailchimp.com/lists?listid=lista1");
        $subscriberList->setWebhookSecret("12345678");
        return $subscriberList->getProvider()->getSubscribers();
    }

    /**
     * {@inheritdoc}
     */
    public function getLists()
    {
        
        $subscriberList  = new SubscriberList('lista1',$this->subscriberProvider);
        $subscriberList->setWebhookUrl("http://us19.admin.mailchimp.com/lists?listid=lista1");
        $subscriberList->setWebhookSecret("12345678");
        return $subscriberList->getProvider()->getSubscribers();
    
    }
}