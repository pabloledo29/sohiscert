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
 * Class UpdateCultivosRecBisCommand
 * @package App\Command
 */
class UpdateCultivosRecBisCommand extends Command
{
    protected static $defaultName = 'gsbase:update:cultivosrecbis';
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
        $this->setName('gsbase:update:cultivosrecbis')
            ->setDescription('Comando que obtiene los registros de la entidad CultivosRec');
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

        $toolsupdate = $em->container->get('toolsupdate');
        $gsbase = $em->container->get('gsbase');
        $gsbasexml = $em->container->get('gsbasexml');

        if ($gsbase->getGsbase() == null) {
            $output->writeln("No se ha podido conectar con el servidor de GsBase");
        }

        $em = $em->container->get('doctrine')->getManager();
        $operatorsCultivosRec = $em->getRepository(Operator::class)->findBy(
            array('opEntity' => 'CultivosRec', 'opEst' => 'C')
        );

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        foreach ($operatorsCultivosRec as $operator) {
            $cultivosRecOperator = $toolsupdate->getCultivoRecBisOperator($gsbase, $gsbasexml, $operator);
            $registersProcessed = $registersProcessed + $cultivosRecOperator['registersProcessed'];
            $registersCreated = $registersCreated + $cultivosRecOperator['registersCreated'];
            $registersUpdated = $registersUpdated + $cultivosRecOperator['registersUpdated'];
        }

        $gsbase->gsbase_stop();
        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);

        fwrite($log, ("CULTIVOSREC => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
                " | Registros Procesados: " . $registersProcessed . " | Registros Creados: " .
                $registersCreated . " | Registros Actualizados: " . $registersUpdated) . "\n");

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
