<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;

/**
 * Class AbstractCvrService.
 */
abstract class AbstractCvrService implements CvrServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function populateClient(Cvr $cvr, ProcessClientCompany $client): ProcessClientCompany
    {
        if (null === $client->getCvr()) {
            $client->setCvr((string) $cvr);
        }

        $result = $this->find($cvr);

        if (empty($result)) {
            return $client;
        }

        $client->setName($result->getName());
        $client->setAddress($result->getAddress());
        $client->setPostalCode($result->getPostalCode());
        $client->setCity($result->getCity());

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function isNewClientInfoAvailable(Cvr $cvr, ProcessClientCompany $client): bool
    {
        $result = $this->find($cvr);

        if (empty($result)) {
            return false;
        }

        $comparisons = [
            $client->getName() => $result->getName(),
            $client->getAddress() => $result->getAddress(),
            $client->getPostalCode() => $result->getPostalCode(),
            $client->getCity() => $result->getCity(),
        ];

        foreach ($comparisons as $key => $value) {
            if (strtolower(trim($key)) !== strtolower(trim($value))) {
                return true;
            }
        }

        return false;
    }
}
