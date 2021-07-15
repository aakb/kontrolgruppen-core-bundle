<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Kontrolgruppen\CoreBundle\Entity\Process;

/**
 * Base class for taxonomy repositories.
 */
abstract class AbstractTaxonomyRepository extends ServiceEntityRepository
{
    /**
     * The taxonomy class for this repository.
     *
     * @var string
     */
    protected static $taxonomyClass;

    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, static::$taxonomyClass);
    }

    /**
     * Find taxonomy by client type.
     *
     * @param string|null $clientType The client type
     *
     * @return mixed
     */
    public function findByClientType(string $clientType = null)
    {
        return $this->findByClientTypes([$clientType]);
    }

    /**
     * Find taxonomy by client types.
     *
     * @param string[] $clientTypes
     *
     * @return mixed
     */
    public function findByClientTypes(array $clientTypes)
    {
        return $this
            ->createFindByClientTypesQueryBuilder($clientTypes)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find taxonomy by process.
     *
     * @param string|null $process
     *
     * @return mixed
     */
    public function findByProcess(Process $process)
    {
        return $this->findByClientType($process->getProcessClient()->getType());
    }

    /**
     * Create a find by client type query builder.
     *
     * @param array $clientTypes
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createFindByClientTypesQueryBuilder(array $clientTypes): QueryBuilder
    {
        $queryBuilder = $this
            ->createQueryBuilder('t', 't.id');

        if (!empty($clientTypes)) {
            $clientTypeCriteria = $queryBuilder->expr()->orX(
                't.clientTypes IS NULL'
            );
            foreach ($clientTypes as $index => $clientType) {
                $placeHolder = 'clientType_'.$index;
                $clientTypeCriteria->add('t.clientTypes LIKE :'.$placeHolder);
                $queryBuilder->setParameter($placeHolder, '%'.json_encode($clientType).'%');
            }
            $queryBuilder->andWhere($clientTypeCriteria);
        }

        return $queryBuilder;
    }
}
