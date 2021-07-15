<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\Conclusion;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Kontrolgruppen\CoreBundle\Entity\User;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Psr\Log\LoggerInterface;

/**
 * Class ProcessManager.
 */
class ProcessManager
{
    private $processRepository;
    private $entityManager;
    private $lockService;
    private $cprService;
    private $logger;
    private $economyService;

    /**
     * ProcessManager constructor.
     *
     * @param ProcessRepository      $processRepository
     * @param EntityManagerInterface $entityManager
     * @param LockService            $lockService
     * @param CprServiceInterface    $cprService
     * @param LoggerInterface        $logger
     * @param EconomyService         $economyService
     */
    public function __construct(ProcessRepository $processRepository, EntityManagerInterface $entityManager, LockService $lockService, CprServiceInterface $cprService, LoggerInterface $logger, EconomyService $economyService)
    {
        $this->processRepository = $processRepository;
        $this->entityManager = $entityManager;
        $this->lockService = $lockService;
        $this->cprService = $cprService;
        $this->logger = $logger;
        $this->economyService = $economyService;
    }

    /**
     * Find ids of processes assigned to user that have not been visited by the user.
     *
     * @param array                                  $processIds
     *   The process ids to limit the search to
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     *   The user
     *
     * @return mixed
     */
    public function getUsersUnvisitedProcessIds(array $processIds, User $user)
    {
        $query = $this->entityManager->createQuery(
            '
            SELECT p.id
            FROM Kontrolgruppen\CoreBundle\Entity\Process p
            WHERE p.caseWorker = :caseWorker
            AND p.id IN (:processIds)
            AND NOT EXISTS (
              SELECT l.id
              FROM Gedmo\Loggable\Entity\LogEntry l
              WHERE l.action = \'read\'
              AND l.username = :username
              AND p.id = l.objectId
            )
            '
        )
            ->setParameter('caseWorker', $user)
            ->setParameter('processIds', $processIds)
            ->setParameter('username', $user->getUsername());

        return array_column($query->getArrayResult(), 'id');
    }

    /**
     * Find open processes assigned to user that has not been visited by the user.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function getUsersUnvisitedProcesses(User $user)
    {
        $query = $this->entityManager->createQuery(
            '
            SELECT p
            FROM Kontrolgruppen\CoreBundle\Entity\Process p
            WHERE p.caseWorker = :caseWorker
            AND NOT EXISTS (
              SELECT l.id
              FROM Gedmo\Loggable\Entity\LogEntry l
              WHERE l.action = \'read\'
              AND l.username = :username
              AND p.id = l.objectId
            )
            '
        )
            ->setParameter('caseWorker', $user)
            ->setParameter('username', $user->getUsername());

        return $query->execute();
    }

    /**
     * @param array $unvisitedProcesses
     * @param array $processes
     *
     * @return array|ArrayCollection
     */
    public function markProcessesAsUnvisited(array $unvisitedProcesses, array $processes)
    {
        $unvisitedProcesses = new ArrayCollection($unvisitedProcesses);
        $processes = new ArrayCollection($processes);

        foreach ($processes as $process) {
            if (!$unvisitedProcesses->contains($process)) {
                $process->setVisitedByCaseWorker(true);
            }
        }

        return $processes;
    }

    /**
     * Create new process.
     *
     * @param Process               $process
     * @param AbstractProcessClient $client
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\Process
     *
     * @throws \Exception
     */
    public function newProcess(Process $process, AbstractProcessClient $client = null)
    {
        $resourceToLock = 'case-number';

        $this->lockService->createLock($resourceToLock);
        // Acquire a blocking lock (cf. https://symfony.com/doc/current/components/lock.html#blocking-locks).
        $this->lockService->acquire($resourceToLock, true);

        if (!$this->lockService->isAcquired($resourceToLock)) {
            throw new \RuntimeException('Could not acquire lock when creating a new Process.');
        }

        $process->setCaseNumber($this->getNewCaseNumber());
        $process->setProcessStatus($this->decideStatusForProcess($process));
        $process->setConclusion($this->createConclusionForProcess($process));
        $process->setProcessClient($client);

        $this->entityManager->persist($process);
        $this->entityManager->flush();

        $this->lockService->release($resourceToLock);

        return $process;
    }

    /**
     * Generate a new case number.
     *
     * @return string case number of format YY-XXXX where YY is the year and XXXX an increasing counter
     *
     * @throws \Exception
     */
    public function getNewCaseNumber()
    {
        $casesInYear = $this->processRepository->findAllFromYear(date('Y'));

        $highestCaseCounter = 0;

        /** @var Process $process */
        foreach ($casesInYear as $process) {
            $caseCounter = $this->getCaseNumberCounterFromProcess($process);
            if ($caseCounter > $highestCaseCounter) {
                $highestCaseCounter = $caseCounter;
            }
        }

        $caseNumber = str_pad($highestCaseCounter + 1, 5, '0', \STR_PAD_LEFT);

        return date('y').'-'.$caseNumber;
    }

    /**
     * Completes a Process.
     *
     * @param Process $process
     */
    public function completeProcess(Process $process)
    {
        $completedAt = new \DateTime();

        // If it's the first time the process is completed,
        // we set the originally completed date.
        if (null === $process->getOriginallyCompletedAt()) {
            $process->setOriginallyCompletedAt($completedAt);
        }

        $process->setCompletedAt($completedAt);
        $process->setLastCompletedAt($completedAt);

        if ($this->isRevenueAvailable($process)) {
            $calculatedRevenue = $this->economyService->calculateRevenue($process);
            $netCollectiveSum = $calculatedRevenue['netCollectiveSum'] ?: null;

            if (!empty($process->getLastNetCollectiveSum())) {
                $netCollectiveSumDifference = $netCollectiveSum - $process->getLastNetCollectiveSum();
                $process->setNetCollectiveSumDifference($netCollectiveSumDifference);
            }

            $process->setLastNetCollectiveSum($netCollectiveSum);
        }

        $this->entityManager->persist($process);
        $this->entityManager->flush();
    }

    /**
     * Decide if revenue is available for a process, i.e. if it makes sense to
     * calculate revenue for the process.
     *
     * @param Process|null $process
     *
     * @return bool
     */
    public function isRevenueAvailable(Process $process = null): bool
    {
        return null !== $process
            && null !== $process->getProcessClient()
            && ProcessClientPerson::PERSON === $process->getProcessClient()->getType();
    }

    /**
     * @param Process $process
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\ProcessStatus|null
     */
    private function decideStatusForProcess(Process $process)
    {
        if (empty($process->getCaseWorker())) {
            return $process->getProcessType()->getDefaultProcessStatusOnEmptyCaseWorker();
        }

        return $process->getProcessType()->getDefaultProcessStatus();
    }

    /**
     * @param Process $process
     *
     * @return int
     */
    private function getCaseNumberCounterFromProcess(Process $process)
    {
        $positionOfDash = strpos($process->getCaseNumber(), '-');
        $processCounter = (int) substr($process->getCaseNumber(), $positionOfDash + 1);

        return $processCounter;
    }

    /**
     * @param Process $process
     *
     * @return Conclusion
     */
    private function createConclusionForProcess(Process $process): Conclusion
    {
        $conclusionClass = $process->getProcessType()->getConclusionClass();

        return new $conclusionClass();
    }
}
