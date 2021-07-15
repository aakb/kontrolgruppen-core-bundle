<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository\Traits;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;

/**
 * Trait FindByProcessTypeTrait.
 */
trait FindByProcessTypeTrait
{
    /**
     * Find taxonomy by process type.
     *
     * @param Process          $process
     * @param ProcessType|null $processType
     *
     * @return ProcessType[]
     */
    public function findByProcessType(Process $process, ProcessType $processType = null)
    {
        if (null === $processType) {
            $processType = $process->getProcessType();
        }

        return $this
            ->createFindByClientTypesQueryBuilder([$process->getProcessClient()->getType()])
            ->andWhere(':processType MEMBER OF t.processTypes')
            ->setParameter('processType', $processType)
            ->getQuery()
            ->getResult();
    }
}
