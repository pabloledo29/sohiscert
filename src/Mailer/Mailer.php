<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Mailer;

use App\Entity\ContactForm;
use App\Entity\UploadedFileRegistry;
use App\Entity\User as EntityUser;
use App\Entity\UserAdmin;
use App\Entity\UserOperator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User;

/**
 * Class Mailer
 * @package App\Mailer
 */
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;
    /**
     * @var \Twig\Environment
     */
    protected $twig;
    /**
     * @var
     */
    protected $email_from_address;
    /**
     * @var
     */
    protected $email_from_name;
    /**
     * @var
     */
    protected $mail_to;


    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     * @param $email_from_address
     * @param $email_from_name
     * @param $mail_to
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig\Environment $twig,
        $email_from_address,
        $email_from_name,
        $mail_to
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->email_from_address = $email_from_address;
        $this->email_from_name = $email_from_name;
        $this->mail_to = $mail_to;
    }

//    /**
//     * @param UserInterface $user
//     */
//    public function sendAdminConfirmationEmailMessage(UserInterface $user)
//    {
//        /**
//         * Custom template using same positioning as
//         * FOSUSerBundle:Registration:email.txt.twig so that the sendEmailMessage
//         * method will break it up correctly
//         */
//        $template = 'BlahBlahUser:Admin:created_user_email.txt.twig';
//        //$url = $this->router->generate('** custom login path**', array(), true);
//        $rendered = $this->templating->render(
//            $template,
//            array(
//                'user' => $user,
//                'password' => $user->getPlainPassword(),
//            )
//        );
//        $this->sendEmailMessage(
//            $rendered,
//            $this->parameters['from_email']['confirmation'],
//            $user->getEmail()
//        );
//    }

    /**
     * @param UserInterface $user
     * @return int|string
     */
    public function sendCreatedUserOperatorEmail(EntityUser $user)
    {
        $parameters = [
            'userName' => $user->getUsername(),
            'plainPassword' => $user->getPassword()
        ];
        
        $to = $user->getEmail();

        $template = $this->twig->load('email/useroperator_created_email.html.twig');
        
        $miMensaje = '
        
        <!DOCTYPE html>
        <html>
        <head>
            <title>Area Privada Sohiscert</title>
        </head>
        <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" background="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-bg-730x1024.jpg">
            <table align="center" border="0" width="650" cellpadding="0" cellspacing="0">
                <tr height="50">
                    <td width="100">
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td>
                        <img id="img2" class="center" valign="bottom" src="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-header-1024x691.jpg" width="550" height="400" >
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" padding="5px" style="padding-left:5%;">
                        <h1><br><font size="50px" face="arial">¡Bienvenido!</font></h1>
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify">
                        <img id="txtwelcomeshc" valign="bottom" src="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/txtwelcomeshc.png" width="550" height="610" >
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">                 
                        <table width="100%" height="40px">
                            <tr>
                                <td bgcolor="#40733c" align="middle" width="150">
                                    <font size="3px" face="arial" color="white" weight="500">Usuario<br><b>' . $parameters['userName'] . '</b></font>
                                </td>
                                <td width="100">
                                    &nbsp;
                                </td>
                                <td bgcolor="#40733c" align="middle" width="150">
                                    <font size="3px" face="arial" color="white" weight="500">Contraseña<br><b>' . $parameters['plainPassword'] . '</b></font>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">                 
                        <table width="100%" height="40px">
                            <tr>
                                <td align="center"><a target="_blank" href="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/docs/ManualdeUsuarioWebAreaClientesSohiscert.pdf" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> DESCARGAR EL MANUAL DE USUARIO</b></font></a></td>
                            </tr>
                            <tr>
                                <td bgcolor="white" border="white">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center"><a target="_blank" href="https://intranet-sohiscert.e4ff.pro-eu-west-1.openshiftapps.com/web/login" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> ACCEDER AL ÁREA PRIVADA DE CLIENTES</b></font></a></td>
                            </tr>
                        </table>
                        <br>
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr height="50">
                    <td width="100">
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';
        
        $subject = $template->renderBlock('subject', $parameters);
        #$bodyHtml = $template->renderBlock('body_html', $parameters);
        #$bodyText = $template->renderBlock('body_text', $parameters);

        try {
            $message = (new \Swift_Message($subject))
                ->setFrom($this->email_from_address, $this->email_from_name)
                ->setTo($to)
                ->setCharset('utf-8')
                #->setBody($bodyHtml, 'text/html')
                ->setBody($miMensaje, 'text/html')
                #->setBody($template->renderView('email/useroperator_created_email.html.twig'),'text/html')
                //->setCc($clientEmail) // Copia a cliente, DESHABILITAR EN PRODUCCIÓN
                ->setBcc(array(
                    'soporte@sohiscert.com' => 'Soporte Sohiscert' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                ));
                #->addPart($miMensaje, 'text/html');
                #->addPart('<q>Por favor utilice un cliente de correo compatible con HTML!!!!</q>', 'text/html');
                

            $response = $this->mailer->send($message);
        } catch (\Exception $ex) {
            
            return $ex->getMessage();
        }

        return $response;
    }

    /**
     * @param UserAdmin $userAdmin
     * @param array $data
     * @return int|string
     */
    public function sendCreatedUserAdminEmail(UserAdmin $userAdmin, array $data)
    {
        $parameters = [
            'userName' => $data['userName'],
            'plainPassword' => $data['plainPassword']
        ];

        $to = $userAdmin->getEmail();
        
        $template = $this->twig->load('email/useradmin_email_created.html.twig');
        
        $miMensaje = '
        
        <!DOCTYPE html>
        <html>
        <head>
            <title>Area Privada Sohiscert</title>
        </head>
        <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" background="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-bg-730x1024.jpg">
            <table align="center" border="0" width="650" cellpadding="0" cellspacing="0">
                <tr height="50">
                    <td width="100">
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td>
                        <img id="img2" class="center" valign="bottom" src="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-header-1024x691.jpg" width="550" height="400" >
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" padding="5px" style="padding-left:5%;">
                        <h1><br><font size="50px" face="arial">¡Bienvenido!</font></h1>
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify">
                        <img id="txtwelcomeshc" valign="bottom" src="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/txtwelcomeshc.png" width="550" height="610" >
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">                 
                        <table width="100%" height="40px">
                            <tr>
                                <td bgcolor="#40733c" align="middle" width="150">
                                    <font size="3px" face="arial" color="white" weight="500">Usuario<br><b>' . $parameters['userName'] . '</b></font>
                                </td>
                                <td width="100">
                                    &nbsp;
                                </td>
                                <td bgcolor="#40733c" align="middle" width="150">
                                    <font size="3px" face="arial" color="white" weight="500">Contraseña<br><b>' . $parameters['plainPassword'] . '</b></font>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">                 
                        <table width="100%" height="40px">
                            <tr>
                                <td align="center"><a target="_blank" href="http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/docs/ManualdeUsuarioWebAreaClientesSohiscert.pdf" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> DESCARGAR EL MANUAL DE USUARIO</b></font></a></td>
                            </tr>
                            <tr>
                                <td bgcolor="white" border="white">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center"><a target="_blank" href="https://intranet-sohiscert.e4ff.pro-eu-west-1.openshiftapps.com/web/login" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> ACCEDER AL ÁREA PRIVADA DE CLIENTES</b></font></a></td>
                            </tr>
                        </table>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        &nbsp;
                    </td>
                    <td bgcolor="white" width="500" align="justify" style="padding-left:5%; padding-right:5%;">
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
                <tr height="50">
                    <td width="100">
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td width="100">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';

        $subject = $template->renderBlock('subject', $parameters);
        #$bodyHtml = $template->renderBlock('body_html', $parameters);
        #$bodyText = $template->renderBlock('body_text', $parameters);

        try {
            $message = (new \Swift_Message($subject))
                ->setFrom($this->email_from_address, $this->email_from_name)
                ->setTo($to)
                ->setCharset('utf-8')
                ->setBody($miMensaje, 'text/html')
                #->setBody($bodyHtml, 'text/html')
                #->setBody($template->renderView('email/useradmin_email_created.html.twig'),'text/html')
                //->setCc($clientEmail) // Copia a cliente, DESHABILITAR EN PRODUCCIÓN
                ->setBcc(array(
                    'soporte@sohiscert.com' => 'Soporte Sohiscert' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic' 
                ));
                #->addPart($miMensaje, 'text/html');
                #->addPart('<q>Por favor utilice un cliente de correo compatible con HTML!!!!</q>', 'text/html');

            $response = $this->mailer->send($message);
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }

        return $response;
    }

    /**
     * @param array $data
     * @param UploadedFileRegistry $file
     * @return string
     */
    public function sendUploadedFileEmail(array $data, UploadedFileRegistry $file)
    {

        $parameters = [
            'operator' => $file->getOpNop(),
            'description' => $file->getDescription(),
            'fileName' => $file->getFileOrigName(),
            'type' => $file->getDocexptype()
        ];

        $to = $this->mail_to;

        $template = $this->twig->load('email/uploadeddoc_email.html.twig');

        $subject = $template->renderBlock('subject', $parameters);
        $bodyHtml = $template->renderBlock('body_html', $parameters);
        $bodyText = $template->renderBlock('body_text', $parameters);

        try { 
            $message = (new \Swift_Message($subject))
                ->setFrom($this->email_from_address, $this->email_from_name)
                ->setTo($to)
                ->setCharset('utf-8')
                ->setBody($bodyHtml, 'text/html')
                //->setCc($clientEmail) // Copia a cliente
                ->setBcc(array(
                    'soporte@sohiscert.com' => 'Soporte Sohiscert' #'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                ))
                ->addPart($bodyText, 'text/plain');

            $response = $this->mailer->send($message);
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }

        return $response;
    }


    /**
     * @param array $data
     * @param UploadedFileRegistry $file
     * @return string
     */
    public function sendFileNotificationTotheOperator(array $data)
    {
        $parameters = [
            'operator' => $data["operator"],
            'tipo' => $data["tipo"],
            'documento' => $data["documento"]
        ];

        $to = $data["opEma"];

        $template = $this->twig->load('email/uploadfileoperator_email.html.twig');

        $subject = $template->renderBlock('subject', $parameters);
        $bodyHtml = $template->renderBlock('body_html', $parameters);
        $bodyText = $template->renderBlock('body_text', $parameters);

        try {
            $message = (new \Swift_Message($subject))
                ->setFrom($this->email_from_address, $this->email_from_name)
                ->setTo($to)
                ->setCharset('utf-8')
                ->setBody($bodyHtml, 'text/html')
                ->setBcc(array(
                    'soporte@sohiscert.com' => 'Soporte Sohiscert' #'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                ))
                ->addPart($bodyText, 'text/plain');

            $response = $this->mailer->send($message);
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }

        return $response;
    }


    /**
     * @param UserOperator $user
     * @param ContactForm $contactForm
     * @return int|string
     */
    public function sendContactFormEmail(UserOperator $user, ContactForm $contactForm)
    {
        $clientEmail = $user->getClientId()->getClEma();
        
        $parameters = [
            'cif' => $user->getUsername(),
            'contactName' => $contactForm->getContactName(),
            'description' => $contactForm->getDescription(),
            'message' => $contactForm->getMessage(),
        ];
        $to = 'sohiscert@sohiscert.com';
        //$to = "consolacion@sohiscert.com"; 
        //$clientEmail = "ignacio.fernandez@atlantic.es";
        
        $template = $this->twig->load('email/contactform_email.html.twig');
        $subject = $template->renderBlock('subject', $parameters);
        
        $bodyHtml = $template->renderBlock('body_html', $parameters);
        $bodyText = $template->renderBlock('body_text', $parameters);
        
        try {
            
            $message =(new \Swift_Message($subject))
                ->setFrom($this->email_from_address, $this->email_from_name)
                ->setTo($to)
                ->setCharset('utf-8')
                ->setBody($bodyHtml, 'text/html') 
                ->setCc($clientEmail) // Copia a cliente
                ->setBcc(array(
                    'soporte@sohiscert.com' => 'Soporte Sohiscert' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                )) 
                ->addPart($bodyText);
                
            $response = $this->mailer->send($message);
           
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }
      
        return $response;
    }





    /**
     * @param User $user
     * @return int|string
     */
    public function sendResettingClientEmail(EntityUser $user)
    {
       /** @var $user App\Entity\User*/
        $parameters = [
            'user' => $user,
            'confirmationUrl' => $user->getConfirmationToken()
        ];
        

        //$to = 'ignacio.fernandez@atlantic.es'; // 'manuel.navarro@atlantic.es'; // 'co.ferrete@atlantic.es';
        $to = $user->getEmail();
        //$clientEmail = 'ignacio.fernandez@atlantic.es'; // 'manuel.navarro@atlantic.es';

        $template = $this->twig->load('email/useroperator_reset_email.html.twig');
        
        $miMensaje = '
        
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>Area Privada Sohiscert</title>
        <style type="text/css" media="screen">
            .sombras2{
            -webkit-box-shadow: 10px 10px 152px -30px rgba(0,0,0,0.75);
            -moz-box-shadow: 10px 10px 152px -30px rgba(0,0,0,0.75);
            box-shadow: 10px 10px 152px -30px rgba(0,0,0,0.75);
            }
         
             #anuncio{
            
             }
             #contain{
             overflow:hidden;
                background-color:white!important;
        /*   display:flex;
             justify-content:center;
             flex-direction:column;
             align-items:center;*/
            
               width:650px;
             min-width:650px;
             max-width:650px;
             
               
             
               /*max-height:1185px;*/
               height:auto;
             }
            
             
             
             #fondo{
                      background-image: url("https://sohiscert.com/wp-content/uploads/2018/07/img-bg-730x1024.jpg");
                    
                     background-size: cover;
                width:650px;
             min-width:650px;
             max-width:650px;
             
               height:auto;
                 padding-right:5%;
                 padding-left:5%;
                 
               padding-top:3%;
               padding-bottom:3%;
             }
             #mensaje{
             text-align: justify;
             text-justify: inter-word;
            
             padding:5%;
             padding-left:10%;
             padding-right:10%;
             margin-bottom:3%;
             padding-bottom:3%;
             margin: 0;
             overflow: auto;
             height: auto;
             }
            
            #mensaje p{
            color:grey;
            font-weight:500;
            
            }
            
            #mensaje h1{
            font-size:50px;
            font-weight:500;
            }
            
             #boton1{
             float:left;
             width:35%;
             height:40px;
             background-color:#40733c;
             text-align:center;
             border-radius: 50px;
             color:white;
             font-weight:bold;
             }
             #boton2{
             float:right;
             width:35%;
             height:40px;
             background-color:#40733c;
             text-align:center;
            border-radius: 50px;
            color:white;
            font-weight:bold;
             }
             #boton3 {
             width:100%;
             height:40px;
             background-color:#40733c;
             text-align:center;
            color:white;
            font-weight:bold;
             }
             #img2{
             width:100%;
             min-width:100%;
             max-width:100%;
             min-height:350px;
             max-height:350px;
             height:350px;
             
             }
        </style>
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" class="background">
        <div id="fondodefondo">
            <div id="fondo">
                <div id="contain" class="sombras2">

                    <div id="imagen">
                        <img id="img2" class=" center" src="https://sohiscert.com/wp-content/uploads/2018/07/img-header-1024x691.jpg" />

                    </div>

                    <div id="mensaje">
                        <h1>Estimado operador</h1>

                       <p>Para restablecer su contraseña por favor pinche el siguiente enlace: <a href="{{path(http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/resetting/' . $parameters['confirmationUrl'].')}}">http://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/resetting/' . $parameters['confirmationUrl'] .'</a></p> 

                       <p>Atentamente,</p>
                       <p>Sohiscert.</p>
                
                    </div>
                    
                </div>
                    
                </div>
            </div>
        </div>
    </body>
    </html>     
        ';
        
        $subject = $template->renderBlock('subject', $parameters);
        //$bodyHtml = $template->renderBlock('body_html', $parameters);
        //$bodyText = $template->renderBlock('body_text', $parameters);
        
        try {
            $message = (new \Swift_Message($subject))
                ->setFrom($this->email_from_address, $this->email_from_name)
                ->setTo($to)
                ->setCharset('utf-8')
                #->setBody($bodyHtml, 'text/html')
                ->setBody($miMensaje, 'text/html')
                #->setBody($template->renderView('email/useroperator_created_email.html.twig'),'text/html')
                #->setCc($clientEmail) // Copia a cliente, DESHABILITAR EN PRODUCCIÓN
                ->setBcc(array(
                    'soporte@sohiscert.com' => 'Soporte Sohiscert' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                ));
                #->addPart($miMensaje, 'text/html');
                #->addPart('<q>Por favor utilice un cliente de correo compatible con HTML!!!!</q>', 'text/html');
                

            $response = $this->mailer->send($message);
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }

        return $response;
    }
}
