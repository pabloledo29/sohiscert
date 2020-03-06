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
use Symfony\Component\DependencyInjection\ContainerBuilder; 

class IniClientOperatorsCommand extends Command
{
    protected static $defaultName = 'app:ini:batch:clientuseroperators';
    public function __construct(string $path_update_logs, $toolsupdate,$gsbase,$gsbasexml)
    {
        $this->path_update_logs= $path_update_logs;
        $this->toolsupdate = $toolsupdate;
        $this->gsbase =$gsbase;
        $this->gsbase =$gsbasexml;
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:ini:batch:clientuseroperators')
            ->setDescription('Vinculación inicial por lotes de clientes y operadores.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $updateStart = date("H:i:s") . substr((string)microtime(), 1, 6);
        $em = new ContainerBuilder();
        $urlBase = $this->path_update_logs;
        $path_file = $urlBase . 'update_' . date("d_m_Y") . '.log';
        #$path_file = __DIR__ . '/../../../app/logs/update/update_' . date("d_m_Y") . '.log';
        $log = fopen($path_file, "a+");
        $lines = file($path_file, FILE_SKIP_EMPTY_LINES);
        $lastLine = trim(array_pop($lines));

        if ($lastLine != 'SI') {
            exit();
        } else {
            fwrite($log, "\n");
            fwrite($log, "NO\n");
        }

        $em = $em->container->get('doctrine')->getManager();

        //$userOperators = $em->getRepository(UserOperator::class)->findBy(array('client_id' => null),null,400);
        $userOperators = $em->getRepository(UserOperator::class)->findBy(array('client_id' => null));

        $registers = 0;

        /** @var UserOperator $userOperator */
        foreach ($userOperators as $userOperator) {

            $operators = $em->getRepository(Operator::class)->findBy(
                array('opCif' => $userOperator->getUsername())
            );

            if (count($operators) > 0) {

                /** Workaround for possible multple clients */
                /** @var Operator $operator */
                $operator = $operators[0];
                $opCcl = $operator->getOpCcl();

                /* Comprobación de client_id asociado creación */
                $toolsupdate = $this->toolsupdate;
                $gsbase = $this->gsbase;
                $gsbasexml = $this->gsbasexml;
                $client = $toolsupdate->getClient($gsbase, $gsbasexml, $userOperator, $opCcl);
                // <1 y no !=1 pues en la BBDD origen se pueden dar CIFs con mas de una ocurrencia.
                if ($client['registersCreated'] >= 1) {
                    $registers += $client['registersCreated'];
                }
            }

            $userOperator->getOperators()->clear();
            foreach ($operators as $operator) {
                $userOperator->addOperator($operator);
            }
            $em->flush();
        }

        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);

        fwrite($log, ("CLIENTES VINCULADOS => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Creados: " . $registers . "\n"));

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
