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
 * Class UpdateBasicInfoCommand
 * @package App\Command
 */
class UpdateBasicInfoCommand extends Command
{
    protected static $defaultName = 'gsbase:update:basicinfo';
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
        $this->setName('gsbase:update:basicinfo')
            ->setDescription('Comando que actualiza la informacion de tablas basicas y pequeñas como: tiposCultivos,
             tiposProductos,etc');
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
        
        if ($gsbase->getGsbase() == null) {
            $output->writeln("No se ha podido conectar con el servidor de GsBase");
        }
        
        $tiposCultivos = $toolsupdate->getTiposCultivos($gsbase, $gsbasexml);
        $tiposProducto = $toolsupdate->getTiposProducto($gsbase, $gsbasexml);
        $cultivos = $toolsupdate->getCultivos($gsbase, $gsbasexml);
        $especies = $toolsupdate->getEspecies($gsbase, $gsbasexml);
        $productosG = $toolsupdate->getProductosG($gsbase, $gsbasexml);
        $tiposProducc = $toolsupdate->getTiposProducc($gsbase, $gsbasexml);
        $actividadesI = $toolsupdate->getActividadesI($gsbase, $gsbasexml);
        
        $gsbase->gsbase_stop();

        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);

        fwrite($log, ("TIPOS_CULTIVOS => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Creados: " . count($tiposCultivos['registersCreated']) . " | Registros Actualizados: " .
            count($tiposCultivos['registersUpdated']) . " | Registros No Actualizados: " .
            count($tiposCultivos['registersNotUpdated']) . "\n"));
        fwrite($log, ("TIPOS_PRODUCTOS => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Creados: " . count($tiposProducto['registersCreated']) . " | Registros Actualizados: " .
            count($tiposProducto['registersUpdated']) . " | Registros No Actualizados: " .
            count($tiposProducto['registersNotUpdated']) . "\n"));
        fwrite($log, ("CULTIVOS => Comienzo: " . $updateStart . " | Final: " . $updateEnd . " | Registros Creados: " .
            count($cultivos['registersCreated']) . " | Registros Actualizados: " .
            count($cultivos['registersUpdated']) . " | Registros No Actualizados: " .
            count($cultivos['registersNotUpdated']) . "\n"));
        fwrite($log, ("ESPECIES => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Creados: " . count($especies['registersCreated']) . " | Registros Actualizados: " .
            count($especies['registersUpdated']) . " | Registros No Actualizados: " .
            count($especies['registersNotUpdated']) . "\n"));
        fwrite($log, ("PRODUCTOSG => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Creados: " . count($productosG['registersCreated']) . " | Registros Actualizados: " .
            count($productosG['registersUpdated']) . " | Registros No Actualizados: " .
            count($productosG['registersNotUpdated']) . "\n"));
        fwrite($log, ("TIPOSPRODUCC => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Creados: " . count($tiposProducc['registersCreated']) . " | Registros Actualizados: " .
            count($tiposProducc['registersUpdated']) . " | Registros No Actualizados: " .
            count($tiposProducc['registersNotUpdated']) . "\n"));
        fwrite($log, ("ACTIVIDADESI => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
            " | Registros Creados: " . count($actividadesI['registersCreated']) . " | Registros Actualizados: " .
            count($actividadesI['registersUpdated']) . " | Registros No Actualizados: " .
            count($actividadesI['registersNotUpdated']) . "\n"));

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
