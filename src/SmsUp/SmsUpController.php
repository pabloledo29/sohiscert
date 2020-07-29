<?php

namespace App\SmsUp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SmsUpController extends AbstractController
{
    /**
     * @Route("/private/smsup/sms/send", name="private_smsup_sms_send")
     * Envío de un SMS para un operador
     */
    public function privatesendSms()
    {
        $sender = $this->get('smsup.smsupapi.sender');
        $sms = $sender->getNewSms()
				->setTexto('Texto del sms')
				->setNumeros(['000000000']);
        $resul = $sender->enviarSms($sms);
        if($resul->getHttpcode()===200){
	        $idenvio = $resul->getResult()[0]['id'];
        }
    }

    /**
     * @Route("/admin/smsup/sms/send", name="admin_smsup_sms_send")
     * Envío de un SMS para un administrador
     */
    public function adminsendSms()
    {
        $sender = $this->get('smsup.smsupapi.sender');
        $sms = $sender->getNewSms()
				->setTexto('Texto del sms')
				->setNumeros(['000000000']);
        $resul = $sender->enviarSms($sms);
        if($resul->getHttpcode()===200){
	        $idenvio = $resul->getResult()[0]['id'];
        }
    }

    /**
     * @Route("/private/smsup/sms/check/{idsms}", name="private_smsup_sms_check")
     * Comprueba el estado de un SMS para un operador
     */
    public function privatecheckSms($idsms){
        $sender = $this->get('smsup.smsupapi.sender');
        $sender->estadoSms($idsms);
    }

     /**
     * @Route("/admin/smsup/sms/check/{idsms}", name="admin_smsup_sms_check")
     * Comprueba el estado de un SMS para un administrador
     */
    public function admincheckSms($idsms){
        $sender = $this->get('smsup.smsupapi.sender');
        $sender->estadoSms($idsms);
    }


    /**
     * @Route("/private/smsup/sms/credit", name="private_smsup_sms_credit")
     * Comprueba tus creditos para un operador
     */
    public function privatecheckCredit(){
        $sender = $this->get('smsup.smsupapi.sender');
        $sender->creditosDisponibles();
    }

    /**
     * @Route("/admin/smsup/sms/credit", name="admin_smsup_sms_credit")
     * Comprueba tus creditos para un administrador
     */
    public function admincheckCredit(){
        $sender = $this->get('smsup.smsupapi.sender');
        $sender->creditosDisponibles();
    }

    /**
     * @Route("/private/smsup/sms/before_result/{$referencia}", name="private_smsup_sms_before_resut")
     * Resultado de la petición anterior para un operador
     */
    public function privatebeforResult($referencia){
        $sender = $this->get('smsup.smsupapi.sender');
        $sender->resultadoPeticion($referencia);
    }

    /**
     * @Route("/admin/smsup/sms/before_result/{$referencia}", name="admin_smsup_sms_before_resut")
     * Resultado de la petición anterior para un administrador
     */
    public function adminbeforResult($referencia){
        $sender = $this->get('smsup.smsupapi.sender');
        $sender->resultadoPeticion($referencia);
    }
}
