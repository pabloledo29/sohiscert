<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;

use App\Entity\Operator;
use App\Entity\UserOperator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 

class IniUserOperatorsCommand extends Command
{
    protected static $defaultName = 'app:ini:batch:useroperators';
    public function __construct(string $path_update_logs,$em)
    {
        $this->path_update_logs= $path_update_logs;
         // you *must* call the parent constructor
         $this->em =$em;
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:ini:batch:useroperators')
            ->setDescription('Generación inicial de usuarios en el sistema con volcado en CSV.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->em;
    
       
        
        $userOperatorCandidates = $em->getRepository(Operator::class)->getOperatosConCifEmailNoUser();

        $usersCreados = 0;
        $users = array();
//        dump($userOperatorCandidates);
        foreach ($userOperatorCandidates as $candidate) {

            $entity = $em->getRepository(UserOperator::class)->findOneBy(array('username' => $candidate['opCif']));
            if (!$entity) {
                /** @var UserOperator $userOperator */
                $userOperator = new UserOperator();
//                dump($candidate);
                /* Generación de password aleatoria */
                $options = [
                    'cost' => 12
                ];
             
                $password = password_hash($this->generateRandomString(8),PASSWORD_BCRYPT,$options); // 8 chars

                $userOperator->setUsername($candidate['opCif']);
                $userOperator->setEmail($candidate['opEma']);
                $userOperator->setPassword($password); // Password para pruebas.
                $userOperator->setEnabled(true);
                $userOperator->addRoles('ROLE_USER');
//                dump($userOperator);
                try {
                    $em->persist($userOperator);
                    $em->flush();
                    $line = [$candidate['opEma'], $candidate['opDenoop'], $candidate['opCif'], $password];
//                    dump($line);
                    array_push($users, $line);
                    $usersCreados++;

                } catch (\Exception $e) {
//                    array_push($users, ['falla', '', '', '']);
                    array_push($users, ['falla', $candidate['opEma'], $candidate['opDenoop'], $candidate['opCif']]);
                }

            }
        }
        exit;
        /** Generación de fichero CSV */
        $urlBase = $this->path_update_logs;
        $path_file = $urlBase . 'update/users_' . date("d_m_Y") . '.log';
        #$path_file = __DIR__ . '/../../../app/logs/update/users_' . date("d_m_Y") . '.csv';

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
//        dump($users);
        return 0;
    }
    //Método con str_shuffle() 
    function generateRandomString($length = 8) { 
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    } 
}
