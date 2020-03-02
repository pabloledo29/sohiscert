<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder; 

use App\Entity\RelationshipRegister;
use App\Entity\Operator;

/**
 * Class UpdateOperatorEntityCommand
 * @package App\Command
 */
class UpdateOperatorEntityCommand extends Command
{
    protected static $defaultName = 'gsbase:update:operatorentity';
    public function __construct()
    {
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('gsbase:update:operatorentity')
            ->setDescription('Comando que le asigna a los operadores las entidades relaciones
             de las que sacar informacion');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $updateStart = date("H:i:s") . substr((string)microtime(), 1, 6);
        $em = new ContainerBuilder();
        $urlBase = $em->getParameter('path_update_logs');
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
        $operators = $em->getRepository(Operator::class)->findAll();

        $operatorsProcessed = 0;

        foreach ($operators as $operator) {
            $relation = $em->getRepository(RelationshipRegister::class)->getRelationByRegSreg(
                $operator->getOpReg(),
                $operator->getOpSreg()
            );
            $operator->setOpEntity($relation['rlEntity']);
            $em->persist($operator);
            $operatorsProcessed++;
        }
        $em->flush();
        $em->clear();
        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);

        fwrite($log, ("RELACIONES_OP_ACTUALIZADAS => Comienzo: " . $updateStart . " | Final: " .
            $updateEnd . " | Registros Procesados: " . $operatorsProcessed . "\n"));
        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
