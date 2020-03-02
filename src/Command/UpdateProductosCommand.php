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

/**
 * Class UpdateProductosCommand
 * @package App\Command
 */
class UpdateProductosCommand extends Command
{
    protected static $defaultName = 'gsbase:update:productos';
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
        $this->setName('gsbase:update:productos')
            ->setDescription('Comando que actualiza la entidad Productos');
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
//        $gsbasexml->getXmlUpdateOperator();

        if ($gsbase->getGsbase() == null) {
            $output->writeln("No se ha podido conectar con el servidor de GsBase");
        }

        $productos = $toolsupdate->getProductos($gsbase, $gsbasexml);
        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);
       
        fwrite($log, ("PRODUCTOS => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Procesados: " . $productos['registersProcessed'] . " | Registros Creados: " .
            $productos['registersCreated'] . " | Registros Actualizados: " . $productos['registersUpdated'] . "\n"));

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}