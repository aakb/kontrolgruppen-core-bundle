<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;

/**
 * @method ProcessStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessStatus[]    findAll()
 * @method ProcessStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessStatusRepository extends AbstractTaxonomyRepository
{
    /**
     * {@inheritdoc}
     */
    protected static $taxonomyClass = ProcessStatus::class;

    /**
     * Get process statuses that are available for the process.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *   The process
     *
     * @return mixed
     *   The process statuses that are available for the process
     */
    public function getAvailableForProcess(Process $process)
    {
        $qb = $this->createQueryBuilder('p')
            ->where(':processType MEMBER OF p.processTypes')
            ->setParameter('processType', $process->getProcessType())
            ->getQuery();

        return $qb->execute();
    }

    /**
     * Get process statuses that are available for completing processes.
     *
     * @param ProcessType $processType
     *
     * @return mixed
     */
    public function getAvailableCompletingStatusForProcessType(ProcessType $processType)
    {
        $qb = $this->createQueryBuilder('p')
            ->where(':processType MEMBER OF p.processTypes')
            ->setParameter(':processType', $processType)
            ->andWhere('p.isCompletingStatus = true');

        return $qb
            ->getQuery()
            ->execute()
        ;
    }
}
