<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;

/**
 * Interface CvrServiceInterface.
 */
interface CvrServiceInterface
{
    /**
     * @param Cvr $cvr
     *
     * @return CvrServiceResultInterface
     *
     * @throws CvrException
     */
    public function find(Cvr $cvr): CvrServiceResultInterface;

    /**
     * Populates client with information from the CVR service. If no data is found via the service the client
     * object is returned without being changed.
     *
     * @param Cvr                  $cvr
     * @param ProcessClientCompany $client
     *
     * @return ProcessClientCompany
     *
     * @throws CvrException
     */
    public function populateClient(Cvr $cvr, ProcessClientCompany $client): ProcessClientCompany;

    /**
     * @param Cvr                   $cvr
     * @param AbstractProcessClient $client
     *
     * @return bool
     *
     * @throws CvrException
     */
    public function isNewClientInfoAvailable(Cvr $cvr, ProcessClientCompany $client): bool;
}
