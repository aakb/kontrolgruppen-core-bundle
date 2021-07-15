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
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Repository\Traits\FindByProcessTypeTrait;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends AbstractTaxonomyRepository
{
    use FindByProcessTypeTrait;

    /**
     * {@inheritdoc}
     */
    protected static $taxonomyClass = Service::class;

    /**
     * Get services by process type.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\ProcessType $processType
     *   The process type
     *
     * @return mixed
     *   Services for a given process type
     */
    public function getByProcessType(ProcessType $processType)
    {
        $qb = $this->createQueryBuilder('service', 'service.id')
            ->where(':processType MEMBER OF service.processTypes')
            ->setParameter('processType', $processType)
            ->getQuery();

        return $qb->getResult();
    }

    /**
     * Get services for a process.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *   The process
     *
     * @return mixed
     *   Services for a process
     */
    public function getByProcess(Process $process)
    {
        $qb = $this->createQueryBuilder('service', 'service.id')
            ->join('\Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry', 'e', 'WITH', 'e.service = service')
            ->where('e.process = :process')
            ->setParameter('process', $process)
            ->getQuery();

        return $qb->getResult();
    }
}
