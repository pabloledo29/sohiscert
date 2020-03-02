<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use App\Entity\RelationshipRegister;
use App\Entity\UserAdmin;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Operator;
use App\Entity\UserOperator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * Class IniController
 *
 * Controllers y métodos para el lanzamiento inicial de la aplicación.
 *
 * @package App\Controller
 */
class IniController extends AbstractController
{
    /**
     * Generación por lotes de usuarios en el sistema.
     *
     * Para Operadores sin usuario vinculado y con carácter de prueba.
     *
     * @return Response
     * @Route("/superadmin/useroperator/batchcreate", name="admin_useroperator_batchcreation")
     */
    public function batchCreateAction()
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass('App\Entity\UserOperator');
        $userManager = $this->container->get('pugx_user_manager');

        $em = $this->getDoctrine()->getManager();
        $userOperatorCandidates = $em->getRepository(Operator::class)->getOperatosConCifEmailNoUser();

        $usersCreados = 0;
        $users = array();
        dump($userOperatorCandidates);
        foreach ($userOperatorCandidates as $candidate) {

            $entity = $em->getRepository(UserOperator::class)->findOneBy(array('username' => $candidate['opCif']));
            if (!$entity) {
                /** @var UserOperator $userOperator */
                $userOperator = $userManager->createUser();
                dump($candidate);
                /* Generación de password aleatoria */
                $tokenGenerator = $this->container->get('util.token_generator');
                $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars

                $userOperator->setUsername($candidate['opCif']);
                $userOperator->setEmail($candidate['opEma']);
                $userOperator->setPassword($password); // Password para pruebas.
                $userOperator->setEnabled(true);
                $userOperator->addRoles('ROLE_USER');

                try {
                    $userManager->updateUser($userOperator, true);
                    array_push($users, [$candidate['opEma'], $candidate['opDenoop'], $candidate['opCif'], $password]);
                    $usersCreados++;

                } catch (\Exception $e) {
                    array_push($users, ['falla', '', '', '']);
                }

            }
        }

        /** Generación de fichero CSV */
        $path_file = __DIR__ . '/../../../app/logs/update/users_' . date("d_m_Y") . '.csv';
//        header('Content-Type: application/excel');
//        header('Content-Disposition: attachment; filename="' . $path_file . '"');

//        $user_CSV[0] = array('first_name', 'last_name', 'age');

//        very simple to increment with i++ if looping through a database result


        $fp = fopen($path_file, 'w');
        foreach ($users as $line) {
            // though CSV stands for "comma separated value"
            // in many countries (including France) separator is ";"
            fputcsv($fp, $line, ',');
        }
        fclose($fp);
        dump($users);

        return $this->render('default/index.html.twig', array(
            'usersCreados' => $usersCreados, 'cifs' => '', 'data' => ''));
    }

    /**
     * Generación en lote de datos de cliente y asociación con operadores de Usuarios del sistema.
     *
     * Vinculación por lotes de Operators con Useropreators ya existentes en el sistema.
     *
     * @return Response
     * @Route("/superadmin/useroperator/batchclientsoperators", name="admin_useroperator_batchclientoperators")
     */
    public function batchGetClientAndOperatorsAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$userOperators = $em->getRepository(UserOperator::class)->findBy(array('client_id' => null),null,400);
        $userOperators = $em->getRepository(UserOperator::class)->findBy(array('client_id' => null));

        /** @var UserOperator $userOperator */
        foreach ($userOperators as $userOperator) {

            $operators = $em->getRepository(Operator::class)->findBy(
                array('opCif' => $userOperator->getUsername())
            );

            if (count($operators) > 0) {

                /** Workaround for possible multple clients */
                $operator = $operators[0];
                $opCcl = $operator->getOpCcl();

                /* Comprobación de client_id asociado creación */
                $toolsupdate = $this->container->get('toolsupdate');
                $gsbase = $this->container->get('gsbase');
                $gsbasexml = $this->container->get('gsbasexml');
                $client = $toolsupdate->getClient($gsbase, $gsbasexml, $userOperator, $opCcl);
                // <1 y no !=1 pues en la BBDD origen se pueden dar CIFs con mas de una ocurrencia.
                if ($client['registersCreated'] < 1) {
                    throw $this->createNotFoundException('Error recuperando los datos del cliente.');
                }
            }

            $userOperator->getOperators()->clear();
            foreach ($operators as $operator) {
                $userOperator->addOperator($operator);
            }
            $em->flush();
        }

        return $this->render('default/index.html.twig', array('usersCreados' => count($userOperators), 'cifs' => ''));
    }

    /**
     * Importa las relaciones a mostrar entre Operator e información a mostrar por subRegistro.
     *
     * Lee desde un csv con la tabla de relaciones la info que se desea mostrar vinculada al expediente de Operador.
     *
     * @Route("/superadmin/importcsv", name="import")
     * @return Response
     */
    public function updateRelationshipRegister()
    {
        $file_handle = fopen(__DIR__ . '/../../../web/uploads/importcsv.csv', "r");
        $em = $this->getDoctrine()->getManager();

        while (!feof($file_handle)) {
            $line = fgetcsv($file_handle, 1024);

            //Previene la inserción de filas en blanco en el final del csv
            if ($line) {
                $rlRegister = new RelationshipRegister($line[0], $line[1], $line[3], $line[4], $line[5], $line[6]);

                $em->persist($rlRegister);
                $em->flush();
                $em->clear();
                //array_push($arr,$line);
                //ld($line);
            }
        }

        fclose($file_handle);

        $allRelationshipRegisters = $this->getDoctrine()->getRepository(RelationshipRegister::class)->findAll();

        return $this->render(
            'public/rlregister.html.twig',
            array('prueba' => "Salida del csv", 'allRelationshipRegisters' => $allRelationshipRegisters)
        );
    }

    /**
     * @return Response
     * @Route("/admin/useroperator/test/create", name="admin_useroperator_test_create")
     */
    public function testCreateUser()
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass('App\Entity\UserOperator');
        $userManager = $this->container->get('pugx_user_manager');

        $em = $this->getDoctrine()->getManager();
//        $cifs = $em->getRepository(Operator::class)->getOperatorsConCif();
        $cifs = ['F99394439', 'B21289707'];

        $operators = array();

        foreach ($cifs as $cif) {
            $entity = $em->getRepository(Operator::class)->findOneBy(array('opCif' => $cif));

            if ($entity) {
                array_push($operators, $entity);
            }
        }
        $usersCreados = 0;

        /** @var Operator $operator */
        foreach ($operators as $operator) {

            $entity = $em->getRepository(UserOperator::class)->findOneBy(
                array('username' => $operator->getOpCif())
            );

            if (!$entity) {

                /* Generación de password aleatoria */
                $tokenGenerator = $this->container->get('util.token_generator');
                $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars

                /** @var UserOperator $userOperator */
                $userOperator = $userManager->createUser();

                $userOperator->setUsername($operator->getOpCif());
                $userOperator->setEmail($operator->getOpEma()); // Comprobar que en la búsqueda el operador tiene email
                $userOperator->setPassword($password); // Password para pruebas.
                $userOperator->setEnabled(true);

                $userManager->updateUser($userOperator, true);
                $usersCreados++;
            }
        }

        return $this->render('default/index.html.twig', array('usersCreados' => $usersCreados, 'cifs' => $cifs));
    }

    /**
     * @return Response
     * @Route("/superadmin/useradmin/test/create", name="superadmin_useroadmin_test_create")
     */
    public function batchCreateUserAdminAction()
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass('App\Entity\UserAdmin');
        $userManager = $this->container->get('pugx_user_manager');

        $mails = [
//            "ana@sohiscert.com",
//            "alfonso@sohiscert.com",
//            "angel@sohiscert.com",
//            "charo@sohiscert.com",
//            "consuelo@sohiscert.com",
//            "contabilidad@sohiscert.com",
//            "e.arnaud@sohiscert.com",
//            "eduardo@sohiscert.com",
//            "elisabeth@sohiscert.com",
//            "franciscojavier@sohiscert.com",
//            "juancarlos@sohiscert.com",
//            "juanlopez@sohiscert.com",
//            "lola@sohiscert.com",
//            "luis@sohiscert.com",
//            "luisflores@sohiscert.com",
//            "m.angeles@sohiscert.com",
//            "mberzosa@sohiscert.com",
//            "mjose@sohiscert.com",
//            "maria@sohiscert.com",
//            "maritza@sohiscert.com",
//            "miguel@sohiscert.com",
//            "nieves@sohiscert.com",
//            "rafa@sohiscert.com",
//            "sohiscert@sohiscert.com",
//            "susana@sohiscert.com",
//            "cristina@sohiscert.com",
//            "juanjo@sohiscert.com",
//            "juanpablo@sohiscert.com",
//            "castillalamancha@sohiscert.com",
//            "vanesa@sohiscert.com",
            "pascual@sohiscert.com",
            "cristina@sohiscert.com"
        ];

        $em = $this->getDoctrine()->getManager();

        $admins = array();
        $data = array();
        $usersCreados = 0;

        foreach ($mails as $mail) {
            $entity = $em->getRepository(UserAdmin::class)->findOneBy(array('email' => $mail));

            if (!$entity) {

                /* Generación de password aleatoria */
                $tokenGenerator = $this->container->get('util.token_generator');
                $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
                $userName = substr($mail, 0, strpos($mail, '@'));

                /** @var UserAdmin $userAdmin */
                $userAdmin = $userManager->createUser();

                $userAdmin->setUsername($userName);
                $userAdmin->setEmail($mail); // Comprobar que en la búsqueda el operador tiene email
                $userAdmin->setPassword($password); // Password para pruebas.
                $userAdmin->setEnabled(true);
                $userAdmin->addRoles('ROLE_ADMIN');
                $userManager->updateUser($userAdmin, true);
                $usersCreados++;
                array_push($admins, $userAdmin);
                array_push($data, [$userName, $mail, $password]);

                $userData = ['userName' => $userName, 'email' => $mail, 'plainPassword' => $password];
                $mailer = $this->container->get('app.mailer.service');
                $mailer->sendCreatedUserAdminEmail($userAdmin, $userData);
            }
        }

        return $this->render(
            'default/index.html.twig',
            array('usersCreados' => $usersCreados, 'cifs' => $admins, 'data' => $data)
        );
    }

    /**
     * @return Response
     * @Route("/superadmin/useradmin/test/registercreate", name="superadmin_useroadmin_test_registercreate")
     */
    public function createRegister()
    {
        $gsBase = $this->get('gsbase');
        $gsBaseXml = $this->get('gsbasexml');
        $toolsUpdate = $this->get('toolsupdate');

        $registers = $toolsUpdate->updateRegister($gsBase, $gsBaseXml);

        return $this->render(
            'default/index.html.twig',
            array('usersCreados' => '', 'cifs' => '', 'data' => '')
        );
    }
}
