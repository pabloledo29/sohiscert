<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 20/04/16
 * Time: 8:38
 */

namespace App\Command;

use App\Entity\Contact;
use App\Entity\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 

class IniContactCommand extends Command
{
    protected static $defaultName = 'app:ini:batch:clientscontact';
    public function __construct(string $path_update_logs,$gsbase,$gsbasexml,$jms_serializer,$em)
    {
        $this->path_update_logs= $path_update_logs;
        $this->gsbase =$gsbase;
        $this->gsbasexml =$gsbasexml;
        $this->jms_serializer =$jms_serializer;
        $this->em = $em;
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:ini:batch:clientscontact')
            ->setDescription('Vinculación inicial por lotes de clientes y contactos.');
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

//        $toolsupdate = $this->toolsupdate;
        $gsbase = $this->gsbase;
        $gsbasexml = $this->gsbasexml;

        if ($gsbase->getGsbase() == null) {
            $output->writeln("No se ha podido conectar con el servidor de GsBase");
        }

        
//        $clients = $em->getRepository('Client::class')->findAll();

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        $xml = $gsbasexml->getXmlRetrieveAllContacts();

        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xml, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $contacts = $this->jms_serializer->deserialize(
            $newXml,
            'App\Entity\RegistroContact',
            'xml'
        );

        /** @var Contact $contact */
        foreach ($contacts->Registro as $contact) {
//            dump($contact->getCnCdcl());
            $client = $em->getRepository(Client::class)->findOneBy(array('codigo' => $contact->getCnCdcl()));

            if ($client) {
                if ($client->getContact() === null) {

                    $em->persist($contact);
                    $em->flush();
                    $client->setContact($contact);
                    $em->flush();
                    $registersCreated++;
//                    dump($client->getCodigo());
                }
            }

//            $contact = $toolsupdate->getContact($gsbase, $gsbasexml, $client);
            $registersProcessed++;
        }

        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);
        fwrite(
            $log,
            ("CONTACT => Comienzo: " . $updateStart . " | Final: " . $updateEnd . " | Registros Procesados: " .
                $registersProcessed . " | Registros Creados: " . $registersCreated . " | Registros Actualizados: " .
                $registersUpdated) . "\n"
        );

        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
