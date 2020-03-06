<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;


use App\Entity\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder; 

/**
 * Class UpdateClientCommand
 * @package App\Command
 */
class UpdateClientCommand extends Command
{
    protected static $defaultName = 'gsbase:update:clients';
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
        $this->setName('gsbase:update:clients')
            ->setDescription('Comando que actualiza la entidad Client');
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

        $em = $em->container->get('doctrine')->getManager();
        $clients = $em->getRepository(Client::class)->findAll();

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        foreach ($clients as $client) {
            $client = $toolsupdate->updateClient($gsbase, $gsbasexml, $client);
            $registersProcessed = $registersProcessed + $client['registersProcessed'];
            $registersCreated = $registersCreated + $client['registersCreated'];
            $registersUpdated = $registersUpdated + $client['registersUpdated'];
        }

        $gsbase->gsbase_stop();
        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);
        fwrite(
            $log,
            ("CLIENT => Comienzo: " . $updateStart . " | Final: " . $updateEnd . " | Registros Procesados: " .
                $registersProcessed . " | Registros Creados: " . $registersCreated . " | Registros Actualizados: " .
                $registersUpdated) . "\n"
        );

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
