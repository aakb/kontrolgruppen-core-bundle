<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
use ItkDev\Serviceplatformen\Service\OnlineService;

/**
 * Class ServiceplatformenCvrService.
 */
class ServiceplatformenCvrService extends AbstractCvrService implements CvrServiceInterface
{
    private $service;

    /**
     * ServiceplatformenCvrService constructor.
     *
     * @param OnlineService $service
     */
    public function __construct(OnlineService $service)
    {
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Cvr $cvr): CvrServiceResultInterface
    {
        try {
            $response = $this->service->getLegalUnit($cvr);
        } catch (ServiceException $e) {
            throw new CvrException($e->getMessage(), $e->getCode(), $e);
        }

        return new ServiceplatformenCvrServiceResult($response);
    }
}
