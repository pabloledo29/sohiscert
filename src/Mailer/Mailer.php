<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Mailer;

use App\Entity\ContactForm;
use App\Entity\UploadedFileRegistry;
use App\Entity\UserAdmin;
use App\Entity\UserOperator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\EscaperExtension;
use Twig\Environment;
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
    public function sendCreatedUserOperatorEmail(UserInterface $user)
    {
        $parameters = [
            'userName' => $user->getUsername(),
            'plainPassword' => $user->getPassword() # ESTABA PUESTO 'plainPassword' => $user->getPassword()
        ];
        
        $to = 'ignacio.fernandez@atlantic.es'; // 'co.ferrete@atlantic.es';
        //$to = $user->getEmail();
        $clientEmail = 'maria.gonzalez@atlantic.es'; // 'manuel.navarro@atlantic.es';

        $template = $this->twig->load('email/useroperator_created_email.html.twig');
        
        $miMensaje = '
        
        <!DOCTYPE html>
        <html>
        <head>
            <title>Area Privada Sohiscert</title>
        </head>
        <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" background="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-bg-730x1024.jpg">
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
                        <img id="img2" class="center" valign="bottom" src="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-header-1024x691.jpg" width="550" height="400" >
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
                        <img id="txtwelcomeshc" valign="bottom" src="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/txtwelcomeshc.png" width="550" height="610" >
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
                                <td align="center"><a target="_blank" href="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/docs/ManualdeUsuarioWebAreaClientesSohiscert.pdf" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> DESCARGAR EL MANUAL DE USUARIO</b></font></a></td>
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
                ->setCc($clientEmail) // Copia a cliente, DESHABILITAR EN PRODUCCIÓN
                ->setBcc(array(
                    'co.ferrete@atlantic.es' => 'Soporte AIT' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
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

        $to = 'ignacio.fernandez@atlantic.es';
        //$to = $userAdmin->getEmail();
        $clientEmail = 'manuel.navarro@atlantic.es';

        $template = $this->twig->load('email/useradmin_email_created.html.twig');
        
        $miMensaje = '
        
        <!DOCTYPE html>
        <html>
        <head>
            <title>Area Privada Sohiscert</title>
        </head>
        <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" background="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-bg-730x1024.jpg">
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
                        <img id="img2" class="center" valign="bottom" src="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-header-1024x691.jpg" width="550" height="400" >
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
                        <img id="txtwelcomeshc" valign="bottom" src="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/txtwelcomeshc.png" width="550" height="610" >
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
                                <td align="center"><a target="_blank" href="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/docs/ManualdeUsuarioWebAreaClientesSohiscert.pdf" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> DESCARGAR EL MANUAL DE USUARIO</b></font></a></td>
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
                ->setCc($clientEmail); // Copia a cliente, DESHABILITAR EN PRODUCCIÓN
                //->setBcc(array(
                    //'soporte@sohiscert.com' => 'Soporte Sohiscert' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic' 
                //));
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

        $to = 'ignacio.fernandez@atlantic.es';
        //$to = $this->mail_to;
        $clientEmail = 'fernando.delalastra@atlantic.es';

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
                ->setCc($clientEmail) // Copia a cliente
                //->setBcc(array(
                //    'soporte@sohiscert.com' => 'Soporte Sohiscert' #'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                //))
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
        #var_dump($data);
        #exit('Parametros Mail');

        $parameters = [
            'operator' => $data["operator"],
            'tipo' => $data["tipo"],
            'documento' => $data["documento"]
        ];

        $to = 'ignacio.fernandez@atlantic.es';
        //$to = $data["opEma"];

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
                #->setBcc(array(
                #    'soporte@sohiscert.com' => 'Soporte Sohiscert' #'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                #))
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
        #$to = 'sohiscert@sohiscert.com';
       // $to = "consolacion@sohiscert.com"; 
        $to = "ignacio.fernandez@atlantic.es";
        $clientEmail = "ignacio.fernandez@atlantic.es";
        
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
                #->setCc($clientEmail) // Copia a cliente
                #->setBcc(array(
                #    '' => 'Soporte Sohiscert' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                #)) 
                ->addPart($bodyText);
                
            $response = $this->mailer->send($message);
           
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }
      
        return $response;
    }





    /**
     * @param UserInterface $user
     * @return int|string
     */
    public function sendResettingClientEmail(UserInterface $user)
    {
        //dump($user);
        //exit('Valores que recupero');

        
        //$url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);


        $parameters = [
            'user' => $user,
            'confirmationUrl' => '$url'
        ];



        $to = 'ignacio.fernandez@atlantic.es'; // 'manuel.navarro@atlantic.es'; // 'co.ferrete@atlantic.es';
        //$to = $user->getEmail();
        $clientEmail = 'ignacio.fernandez@atlantic.es'; // 'manuel.navarro@atlantic.es';

        $template = $this->twig->load('email/useroperator_created_email.html.twig');
        
        $miMensaje = '
        
        <!DOCTYPE html>
        <html>
        <head>
            <title>Area Privada Sohiscert - Restaurar Contraseña Usuario</title>
        </head>
        <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" background="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-bg-730x1024.jpg">
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
                        <img id="img2" class="center" valign="bottom" src="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/img-header-1024x691.jpg" width="550" height="400" >
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
                        <img id="txtwelcomeshc" valign="bottom" src="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/ images/txtwelcomeshc.png" width="550" height="610" >
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
                                    <font size="3px" face="arial" color="white" weight="500">Usuario<br>' . $parameters['user'] . '</font>
                                </td>
                                <td width="100">
                                    &nbsp;
                                </td>
                                <td bgcolor="#40733c" align="middle" width="150">
                                    <font size="3px" face="arial" color="white" weight="500">Contraseña<br>' . $parameters['confirmationUrl'] . '</font>
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
                        <table bgcolor="#40733c" width="100%" height="40px">
                            <tr>
                                <td align="center"><a target="_blank" href="http://intranet-pre-intranetshc.e4ff.pro-eu-west-1.openshiftapps.com/public/docs/ManualdeUsuarioWebAreaClientesSohiscert.pdf" style="text-decoration:none"><font size="3px" face="arial" color="white" weight="500"> DESCARGAR EL MANUAL DE USUARIO</font></a></td>
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
                ->setBody($miMensaje, 'text/html');
                #->setBody($template->renderView('email/useroperator_created_email.html.twig'),'text/html')
                #->setCc($clientEmail) // Copia a cliente, DESHABILITAR EN PRODUCCIÓN
                #->setBcc(array(
                #    'soporte@sohiscert.com' => 'Soporte Sohiscert' # 'fernando.delalastra@atlantic.es'  => 'Soporte Atlantic'
                #));
                #->addPart($miMensaje, 'text/html');
                #->addPart('<q>Por favor utilice un cliente de correo compatible con HTML!!!!</q>', 'text/html');
                

            $response = $this->mailer->send($message);
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }

        return $response;
    }
}
