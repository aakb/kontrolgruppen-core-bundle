<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;

/**
 * Class AbstractCprService.
 */
abstract class AbstractCprService implements CprServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function populateClient(Cpr $cpr, ProcessClientPerson $client): ProcessClientPerson
    {
        if (null === $client->getCpr()) {
            $client->setCpr((string) $cpr);
        }

        $result = $this->find($cpr);

        if (empty($result)) {
            return $client;
        }

        $firstName = $result->getFirstName();
        if (null !== $result->getMiddleName()) {
            $firstName .= ' '.$result->getMiddleName();
        }

        $client->setFirstName($firstName);
        $client->setLastName($result->getLastName());
        $client->setAddress($result->getAddress());
        $client->setPostalCode($result->getPostalCode());
        $client->setCity($result->getCity());

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function isNewClientInfoAvailable(Cpr $cpr, ProcessClientPerson $client): bool
    {
        $result = $this->find($cpr);

        if (empty($result)) {
            return false;
        }

        $firstName = $result->getFirstName();
        if (null !== $result->getMiddleName()) {
            $firstName .= ' '.$result->getMiddleName();
        }

        $comparisons = [
            $client->getFirstName() => $firstName,
            $client->getLastName() => $result->getLastName(),
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
