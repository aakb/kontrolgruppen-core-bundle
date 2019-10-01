<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Repository\ProcessLogEntryRepository;

class LogManager
{
    protected $entityManager;
    protected $processLogEntryRepository;

    /**
     * LogManager constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, ProcessLogEntryRepository $processLogEntryRepository)
    {
        $this->entityManager = $entityManager;
        $this->processLogEntryRepository = $processLogEntryRepository;
    }

    public function attachProcessStatusChangesToJournalEntries($result, $process, $sortDirection)
    {
        // Merged log entries into result.
        $processLogEntries = $this->processLogEntryRepository->getLatestLogEntries(
            $process,
            ProcessLogEntryLevelEnumType::NOTICE
        )->getArrayResult();

        foreach ($processLogEntries as $logEntry) {
            if (isset($logEntry['logEntry']['data']['processStatus'])) {
                $result[] = $logEntry;
            }
        }

        // Sort the results.
        usort($result, function ($a, $b) use ($sortDirection) {
            if ($a['updatedAt'] > $b['updatedAt']) {
                return 'desc' === $sortDirection ? -1 : 1;
            } else {
                if ($a['updatedAt'] < $b['updatedAt']) {
                    return 'desc' !== $sortDirection ? -1 : 1;
                }
            }

            return 0;
        });

        return $result;
    }

    public function attachLogEntriesToJournalEntries($journalEntries)
    {
        $journalEntryIds = array_reduce(
            $journalEntries,
            function ($carry, $item) {
                $carry[] = $item['id'];

                return $carry;
            },
            []
        );
        $qb = $this->entityManager->createQueryBuilder('log');
        $qb->select('log')->from(LogEntry::class, 'log')
            ->where('log.objectId IN (:journalEntryIds)')
            ->orderBy('log.id', 'desc')
            ->andWhere('log.objectClass = \''.JournalEntry::class.'\'');
        $qb->setParameter('journalEntryIds', $journalEntryIds);
        $logs = $qb->getQuery()->execute();
        foreach ($logs as $log) {
            if (!isset($journalEntries[$log->getObjectId()]['logs'])) {
                $journalEntries[$log->getObjectId()]['logs'] = [];
            }
            $journalEntries[$log->getObjectId()]['logs'][] = $log;
        }

        return $journalEntries;
    }

    public function attachLogEntriesToJournalEntry($journalEntry)
    {
        $qb = $this->entityManager->createQueryBuilder('log');
        $qb->select('log')->from(LogEntry::class, 'log')
            ->where('log.objectId = :journalEntryId')
            ->orderBy('log.id', 'desc')
            ->andWhere('log.objectClass = \''.JournalEntry::class.'\'');
        $qb->setParameter('journalEntryId', $journalEntry->getId());
        $logs = $qb->getQuery()->execute();

        if (!isset($journalEntries->logs)) {
            $journalEntry->logs = [];
        }

        foreach ($logs as $log) {
            $journalEntry->logs[] = $log;
        }

        return $journalEntry;
    }
}
