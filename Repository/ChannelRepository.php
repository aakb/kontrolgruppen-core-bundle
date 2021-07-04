<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\Channel;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\Traits\FindByProcessTypeTrait;

/**
 * @method Channel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Channel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Channel[]    findAll()
 * @method Channel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChannelRepository extends AbstractTaxonomyRepository
{
    use FindByProcessTypeTrait;

    /**
     * {@inheritdoc}
     */
    protected static $taxonomyClass = Channel::class;

    /**
     * Get channels by process type.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\ProcessType $processType
     *   The process type
     *
     * @return mixed
     */
    public function getByProcessType(ProcessType $processType)
    {
        $qb = $this->createQueryBuilder('channel', 'channel.id')
            ->where(':processType MEMBER OF channel.processTypes')
            ->setParameter('processType', $processType)
            ->getQuery();

        return $qb->getResult();
    }
}
