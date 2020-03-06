<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 

use App\Entity\Operator;
use App\Entity\UserOperator;

/**
 * Class RemoveOperatorCommand
 * @package App\Command
 */
class RemoveOperatorCommand extends Command
{
    protected static $defaultName = 'gsbase:remove:operator';
    public function __construct(string $path_update_logs,$gsbase,$gsbasexml,$jms_serializer,$em)
    {
        $this->path_update_logs= $path_update_logs;
        $this->gsbase =$gsbase;
        $this->gsbasexml =$gsbasexml;
        $this->jms_serializer = $jms_serializer;
        $this->em=$em;
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('gsbase:remove:operator')
            ->setDescription('Comando que compara los operadores y elimina los que tengan un estado = B');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $updateStart = date("H:i:s") . substr((string)microtime(), 1, 6);
        $em = $this->em;
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

        $gsbase = $this->gsbase;
        $gsbasexml = $this->gsbasexml;
        $xml = $gsbasexml->getXmlRemoveOperator();

        if ($gsbase->getGsbase() == null) {
            $output->writeln("No se ha podido conectar con el servidor de GsBase");
        }

        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xml, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $operators = $this->jms_serializer->deserialize(
            $newXml,
            'App\Entity\RegistroOperator',
            'xml'
        );

    
        $operatorsDeleted = 0;
        $operatorsProcessed = 0;

        /** @var Operator $operatorXml */
        foreach ($operators->Registro as $operatorXml) {
            $operatorToRemove = $em->getRepository(Operator::class)->findOneBy(
                array('codigo' => $operatorXml->getCodigo())
            );
            if ($operatorToRemove) {
                // Eliminado primero de UserOperator.
                $userOperator = $em->getRepository(UserOperator::class)->findOneBy(
                    array('username' => $operatorToRemove->getOpCif())
                );
                if ($userOperator) {
                    $userOperator->removeOperator($operatorToRemove);
                }

                $em->remove($operatorToRemove);
                $operatorsDeleted++;
            }
            $operatorsProcessed++;
        }
        $em->flush();
        $em->clear();

        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);
        fwrite(
            $log,
            ("OPERADORESREMOVE => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
                " | Registros Procesados: " . $operatorsProcessed . " | Registros Eliminados: " .
                $operatorsDeleted . "\n")
        );
        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
