<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;

use App\Entity\Operator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder; 

/**
 * Class UpdateIndustriasCommand
 * @package App\Command
 */
class UpdateIndustriasCommand extends Command
{
    protected static $defaultName = 'gsbase:update:industrias';
    public function __construct(string $path_update_logs)
    {
        $this->path_update_logs= $path_update_logs;
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('gsbase:update:industrias')
            ->setDescription('Comando que actualiza la entidad Industrias');
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

        $toolsupdate = $em->container->get('toolsupdate');
        $gsbase = $em->container->get('gsbase');
        $gsbasexml = $em->container->get('gsbasexml');
        $em = $em->container->get('doctrine')->getManager();

        $operatorsIndustrias = $em->getRepository(Operator::class)->findBy(
            array('opEntity' => 'Industrias', 'opEst' => 'C')
        );

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        foreach ($operatorsIndustrias as $operator) {
            $industriasOperator = $toolsupdate->getIndustriasOperator($gsbase, $gsbasexml, $operator);
            $registersUpdated = $registersUpdated + $industriasOperator['registersUpdated'];
            $registersCreated = $registersCreated + $industriasOperator['registersCreated'];
            $registersProcessed = $registersProcessed + $industriasOperator['registersProcessed'];
        }

        $gsbase->gsbase_stop();
        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);
        fwrite($log, ("INDUSTRIAS => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Procesados: " . $registersProcessed . " | Registros Creados: " . $registersCreated .
            " | Registros Actualizados: " . $registersUpdated . "\n"));

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
