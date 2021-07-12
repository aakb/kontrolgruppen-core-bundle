<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\CVR\Cvr;
use Kontrolgruppen\CoreBundle\CVR\CvrServiceInterface;
use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class ProcessClientManager.
 */
class ProcessClientManager
{
    private $entityManager;
    private $cprService;
    private $cvrService;
    private $propertyAccessor;
    private $logger;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface    $entityManager
     * @param CprServiceInterface       $cprService
     * @param CvrServiceInterface       $cvrService
     * @param PropertyAccessorInterface $propertyAccessor
     * @param LoggerInterface           $logger
     */
    public function __construct(EntityManagerInterface $entityManager, CprServiceInterface $cprService, CvrServiceInterface $cvrService, PropertyAccessorInterface $propertyAccessor, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->cprService = $cprService;
        $this->cvrService = $cvrService;
        $this->propertyAccessor = $propertyAccessor;
        $this->logger = $logger;
    }

    /**
     * Create a process client.
     *
     * @param string $type       The client type ("company" or "person")
     * @param array  $properties The client properties
     *
     * @return abstractProcessClient The client
     *
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function createClient(string $type, array $properties = []): AbstractProcessClient
    {
        switch ($type) {
            case ProcessClientCompany::TYPE:
                $client = new ProcessClientCompany();
                if ($cvr = $properties['cvr'] ?? null) {
                    $this->cvrService->populateClient(new Cvr($cvr), $client);
                }
                break;

            case ProcessClientPerson::TYPE:
                $client = new ProcessClientPerson();
                if ($cpr = $properties['cpr'] ?? null) {
                    $this->cprService->populateClient(new Cpr($cpr), $client);
                }
                break;

            default:
                throw new \RuntimeException(sprintf('Invalid client type: %s', $type));
        }

        foreach ($properties as $name => $value) {
            $this->propertyAccessor->setValue($client, $name, $value);
        }

        return $client;
    }

    /**
     * Populate client with data from lookup service.
     *
     * @param AbstractProcessClient $client
     *
     * @return AbstractProcessClient
     */
    public function populateClient(AbstractProcessClient $client): AbstractProcessClient
    {
        if ($client instanceof ProcessClientCompany) {
            return $this->cvrService->populateClient(new Cvr($client->getCvr()), $client);
        }
        if ($client instanceof ProcessClientPerson) {
            return $this->cprService->populateClient(new Cpr($client->getCpr()), $client);
        }
    }

    /**
     * Check if new client data is available in lookup service.
     *
     * @param AbstractProcessClient $client
     *
     * @return bool
     */
    public function isNewClientInfoAvailable(AbstractProcessClient $client): bool
    {
        if ($client instanceof ProcessClientCompany) {
            return $client->getCvr() && $this->cvrService->isNewClientInfoAvailable(new Cvr($client->getCvr()), $client);
        }
        if ($client instanceof ProcessClientPerson) {
            return $client->getCpr() && $this->cprService->isNewClientInfoAvailable(new Cpr($client->getCpr()), $client);
        }
    }

    /**
     * Get client types as a map from (short) name to class name.
     *
     * @return array
     */
    public static function getClientTypes(): array
    {
        return [
            ProcessClientPerson::PERSON => ProcessClientPerson::class,
            ProcessClientCompany::COMPANY => ProcessClientCompany::class,
        ];
    }

    /**
     * Find all process clients.
     *
     * @return array
     */
    public function findAll(): array
    {
        $clients = [];

        foreach (static::getClientTypes() as $class) {
            $repository = $this->entityManager->getRepository($class);
            $clients[] = $repository->findAll();
        }

        return array_merge(...$clients);
    }
}
