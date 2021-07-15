<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Service\ProcessClientManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ClientUpdateCommand.
 */
class ClientUpdateCommand extends Command
{
    protected static $defaultName = 'kontrolgruppen:client:update';
    private $processClientManager;
    private $entityManager;
    private $logger;

    /**
     * ClientUpdateCommand constructor.
     *
     * @param ProcessClientManager   $processClientManager
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface        $logger
     */
    public function __construct(ProcessClientManager $processClientManager, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->processClientManager = $processClientManager;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Updated clients with (if any) new information from the CPR service');
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Performs dry run where service calls is performed but nothing is stored in the database.'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isDryRun = !empty($input->getOption('dry-run'));

        $clients = $this->processClientManager->findAll();
        $updatedClients = [];

        foreach ($clients as $client) {
            try {
                $newInfoAvailable = $this->processClientManager->isNewClientInfoAvailable($client);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            if (!$newInfoAvailable) {
                continue;
            }

            try {
                $updatedClient = $this->processClientManager->populateClient($client);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            if (!$isDryRun) {
                $this->entityManager->persist($updatedClient);
            }

            $updatedClients[] = $updatedClient;

            if ($output->isVeryVerbose()) {
                $output->writeln(sprintf('Client with ID: %s was updated.', $client->getId()));
            }
        }

        if ($output->isVerbose()) {
            $output->writeln(sprintf('%s client(s) was updated.', \count($updatedClients)));
        }

        if (!$isDryRun) {
            $this->entityManager->flush();
        }

        return 0;
    }
}
