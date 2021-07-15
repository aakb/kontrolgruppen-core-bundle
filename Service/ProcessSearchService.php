<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;

/**
 * Class ProcessSearchService.
 */
class ProcessSearchService
{
    private $paginator;
    private $processRepository;

    /**
     * ProcessSearchService constructor.
     *
     * @param PaginatorInterface $paginator
     * @param ProcessRepository  $processRepository
     */
    public function __construct(PaginatorInterface $paginator, ProcessRepository $processRepository)
    {
        $this->paginator = $paginator;
        $this->processRepository = $processRepository;
    }

    /**
     * Search fuzzy.
     *
     * @param string $search
     * @param int    $page
     * @param int    $limit
     *
     * @return PaginationInterface
     */
    public function searchFuzzy(string $search, int $page = 1, $limit = 50): PaginationInterface
    {
        $qb = $this->getQueryBuilder();

        $fieldMatches = $this->getFieldMatches($search);

        if (\count($fieldMatches) > 0) {
            $qb = $this->applyFieldSearch($qb, $fieldMatches);
        }

        $qb->orWhere('e.caseNumber LIKE :search');
        $qb->orWhere('client.telephone LIKE :search');
        $qb->orWhere('client.name LIKE :search');
        $qb->orWhere('client.identifier LIKE :search');
        $qb->orWhere('client.address LIKE :search');
        $qb->orWhere('caseWorker.username LIKE :search');
        $qb->setParameter(':search', '%'.$search.'%');

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    /**
     * Search precise.
     *
     * @param string $search
     * @param int    $page
     * @param int    $limit
     *
     * @return PaginationInterface
     */
    public function searchPrecise(string $search, int $page = 1, $limit = 50): PaginationInterface
    {
        $qb = $this->getQueryBuilder();

        $fieldMatches = $this->getFieldMatches($search);

        if (\count($fieldMatches) > 0) {
            $qb = $this->applyFieldSearch($qb, $fieldMatches);
        } else {
            $qb->orWhere('client.address = :search');
            $qb->orWhere('client.name = :search');
            $qb->setParameter(':search', $search);
        }

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    /**
     * Add orWhere based on matches.
     *
     * @param QueryBuilder $queryBuilder
     *   The query builder
     * @param array        $matches
     *   Array of matches
     *
     * @return QueryBuilder
     */
    private function applyFieldSearch(QueryBuilder $queryBuilder, array $matches): QueryBuilder
    {
        if (isset($matches['caseNumber'])) {
            $queryBuilder->orWhere('e.caseNumber = :search_case_number_alternative');
            $queryBuilder->setParameter(':search_case_number_alternative', $matches['caseNumber']);
        }
        if (isset($matches['cpr'])) {
            $queryBuilder->orWhere('e.clientCPR = :search_cpr_alternative');
            $queryBuilder->setParameter(':search_cpr_alternative', $matches['cpr']);
        }
        if (isset($matches['telephone'])) {
            $queryBuilder->orWhere('client.telephone = :search_telephone_alternative');
            $queryBuilder->setParameter(':search_telephone_alternative', $matches['telephone']);
        }

        return $queryBuilder;
    }

    /**
     * Get possible matches between fields and the search.
     *
     * @param string $search
     *   The search string
     *
     * @return array
     */
    private function getFieldMatches(string $search): array
    {
        preg_match('/^\d{2}-?\d{5}$/', $search, $possibleCaseNumberMatches);
        preg_match('/^\d{6}-?\d{4}$/', $search, $possibleCPRMatches);
        preg_match('/^\d{8}$/', $search, $possiblePhoneNumberMatches);

        $result = [];
        if (1 === \count($possibleCaseNumberMatches)) {
            $match = $possibleCaseNumberMatches[0];

            // Add '-' if missing.
            if (7 === \strlen($match)) {
                $match = substr($match, 0, 2).'-'.substr($match, 2, 5);
            }

            $result['caseNumber'] = $match;
        }
        if (1 === \count($possibleCPRMatches)) {
            $match = $possibleCPRMatches[0];

            // Add '-' if missing.
            if (10 === \strlen($match)) {
                $match = substr($match, 0, 6).'-'.substr($match, 6, 4);
            }

            $result['cpr'] = $match;
        }
        if (1 === \count($possiblePhoneNumberMatches)) {
            $result['telephone'] = $possiblePhoneNumberMatches[0];
        }

        return $result;
    }

    /**
     * Get a query builder for Process.
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->processRepository->createQueryBuilder('e');
        $qb->leftJoin('e.processClient', 'client');
        $qb->addSelect('client');
        $qb->leftJoin('e.caseWorker', 'caseWorker');
        $qb->addSelect('partial caseWorker.{id,username,name}');

        // Add sortable fields.
        $qb->leftJoin('e.channel', 'channel');
        $qb->addSelect('partial channel.{id,name}');

        $qb->leftJoin('e.service', 'service');
        $qb->addSelect('partial service.{id,name}');

        $qb->leftJoin('e.processType', 'processType');
        $qb->addSelect('partial processType.{id,name}');

        $qb->leftJoin('e.processStatus', 'processStatus');
        $qb->addSelect('partial processStatus.{id,name}');

        return $qb;
    }
}
