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
 * Class UpdateProductosIndusCommand
 * @package App\Command
 */
class UpdateProductosIndusCommand extends Command
{
    protected static $defaultName = 'gsbase:update:productosindus';
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
        $this->setName('gsbase:update:productosindus')
            ->setDescription('Comando que actualiza la entidad ProductosIndus');
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

        $toolsupdate = $this->toolsupdate;
        $gsbase = $this->gsbase;
        $gsbasexml = $this->gsbasexml;

        if ($gsbase->getGsbase() == null) {
            $output->writeln("No se ha podido conectar con el servidor de GsBase");
        }

        $em = $em->container->get('doctrine')->getManager();
        $operatorsProductosIndus = $em->getRepository(Operator::class)->findBy(
            array('opEntity' => 'ProductosIndus', 'opEst' => 'C')
        );

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        foreach ($operatorsProductosIndus as $operator) {
            $ProductosIndusOperator = $toolsupdate->getProductosIndusOperator($gsbase, $gsbasexml, $operator);
            $registersProcessed = $registersProcessed + $ProductosIndusOperator['registersProcessed'];
            $registersCreated = $registersCreated + $ProductosIndusOperator['registersCreated'];
            $registersUpdated = $registersUpdated + $ProductosIndusOperator['registersUpdated'];
        }

        $gsbase->gsbase_stop();
        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);
        fwrite(
            $log,
            ("PRODUCTOSINDUS => Comienzo: " . $updateStart . " | Final: " . $updateEnd . " | Registros Procesados: " .
                $registersProcessed . " | Registros Creados: " . $registersCreated . " | Registros Actualizados: " .
                $registersUpdated) . "\n"
        );

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
