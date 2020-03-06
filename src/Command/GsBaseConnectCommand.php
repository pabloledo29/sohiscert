<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GsBaseConnectCommand
 * @package App\Command
 */
class GsBaseConnectCommand extends Command
{
    protected static $defaultName = 'gsbase:connect';
    public function __construct(string $path_update_logs,$gsbase)
    {
        $this->path_update_logs = $path_update_logs;
        $this->gsbase=$gsbase;
 

        
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('gsbase:connect')
        ->setDescription('Comando que comprueba si hay conexion con GsBase');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $urlBase = $this->path_update_logs;
        $path_file = $urlBase . 'update_' . date("d_m_Y") . '.log';
        #$path_file = __DIR__ . '/../../../app/logs/update/update_' . date("d_m_Y") . '.log';
        $log = fopen($path_file, "a+");
        $lines = file($path_file, FILE_SKIP_EMPTY_LINES);
        trim(array_pop($lines));

        $gsbase = $this->gsbase;
        if ($gsbase->getGsbase() == null) {
            var_dump("NO");
            $res = "\nNO";
        } else {
            var_dump("SI");
            $res = "\nSI";
        }
        fwrite($log, $res);
        fclose($log);
        
        return 0;
    }
}

