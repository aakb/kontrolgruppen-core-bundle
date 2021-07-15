<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export\Reports;

use Doctrine\ORM\EntityManagerInterface;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Exception;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Psr\Cache\CacheItemPoolInterface;
use Traversable;

/**
 * Class RevenueExport.
 */
class RevenueExport extends AbstractExport
{
    protected $title = 'Samlet provenue opdelt på ydelser';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /**
     * RevenueExport constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EconomyService         $economyService
     * @param CacheItemPoolInterface $cachePhpspreadsheet
     *
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, EconomyService $economyService, CacheItemPoolInterface $cachePhpspreadsheet)
    {
        parent::__construct($cachePhpspreadsheet);
        $this->entityManager = $entityManager;
        $this->economyService = $economyService;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return parent::getParameters();
    }

    /**
     * {@inheritdoc}
     */
    public function writeData()
    {
        $processes = $this->getProcesses();

        $this->writeHeader([
            'Ydelse',
            'Antal afsluttede sager',
            'Samlet tilbagebetalingskrav (Brutto)',
            'Samlet fremadrettet besparelse (Brutto)',
            'Samlet provenu (Brutto)',
            'Provenu pr. sag (Brutto)',
            'Samlet tilbagebetalingskrav (Netto)',
            'Samlet fremadrettet besparelse (Netto)',
            'Samlet provenu (Netto)',
            'Provenu pr. sag (Netto)',
        ]);

        $revenue = [];

        $processesLength = 0;
        foreach ($processes as $process) {
            $countedForService = [];
            $processRevenue = $this->economyService->calculateRevenue($process);

            foreach ($processRevenue['repaymentSums'] as $serviceName => $repaymentSum) {
                if (!isset($revenue[$serviceName])) {
                    $revenue[$serviceName] = $this->newEntry();
                }

                if (!isset($countedForService[$serviceName])) {
                    ++$revenue[$serviceName]['processes'];
                    $countedForService[$serviceName] = $serviceName;
                }
                $revenue[$serviceName]['collectiveRepaymentSum'] += $repaymentSum['sum'];
                $revenue[$serviceName]['collectiveRevenueSum'] += $repaymentSum['sum'];
                $revenue[$serviceName]['netCollectiveRepaymentSum'] += $repaymentSum['sum'] * $repaymentSum['netPercentage'] / 100.0;
                $revenue[$serviceName]['netCollectiveRevenueSum'] += $repaymentSum['sum'] * $repaymentSum['netPercentage'] / 100.0;
            }

            foreach ($processRevenue['futureSavingsSums'] as $serviceName => $futureSavingsSum) {
                if (!isset($revenue[$serviceName])) {
                    $revenue[$serviceName] = $this->newEntry();
                }

                if (!isset($countedForService[$serviceName])) {
                    ++$revenue[$serviceName]['processes'];
                    $countedForService[$serviceName] = $serviceName;
                }
                $revenue[$serviceName]['collectiveFutureSavingsSum'] += $futureSavingsSum['sum'];
                $revenue[$serviceName]['collectiveRevenueSum'] += $futureSavingsSum['sum'];
                $revenue[$serviceName]['netCollectiveFutureSavingsSum'] += $futureSavingsSum['sum'] * $futureSavingsSum['netPercentage'] / 100.0;
                $revenue[$serviceName]['netCollectiveRevenueSum'] += $futureSavingsSum['sum'] * $futureSavingsSum['netPercentage'] / 100.0;
            }

            if (empty($processRevenue['repaymentSums']) && empty($processRevenue['futureSavingsSums'])) {
                $serviceName = $process->getService() ? $process->getService()->getName() : '-- Ikke sat --';
                if (!isset($revenue[$serviceName])) {
                    $revenue[$serviceName] = $this->newEntry();
                }
                ++$revenue[$serviceName]['processes'];
            }

            ++$processesLength;
        }

        $sums = [
            'collectiveRepaymentSum' => 0,
            'collectiveFutureSavingsSum' => 0,
            'collectiveRevenueSum' => 0,
            'netCollectiveRepaymentSum' => 0,
            'netCollectiveFutureSavingsSum' => 0,
            'netCollectiveRevenueSum' => 0,
        ];

        foreach ($revenue as $key => $value) {
            $value['revenueAverage'] = $value['collectiveRevenueSum'] / $value['processes'];
            $value['netRevenueAverage'] = $value['netCollectiveRevenueSum'] / $value['processes'];

            $this->writeRow([
                $key,
                $this->formatNumber($value['processes'], 0),
                $this->formatNumberWithThousandSeparators($value['collectiveRepaymentSum']),
                $this->formatNumberWithThousandSeparators($value['collectiveFutureSavingsSum']),
                $this->formatNumberWithThousandSeparators($value['collectiveRevenueSum']),
                $this->formatNumberWithThousandSeparators($value['revenueAverage']),
                $this->formatNumberWithThousandSeparators($value['netCollectiveRepaymentSum']),
                $this->formatNumberWithThousandSeparators($value['netCollectiveFutureSavingsSum']),
                $this->formatNumberWithThousandSeparators($value['netCollectiveRevenueSum']),
                $this->formatNumberWithThousandSeparators($value['netRevenueAverage']),
            ]);

            $sums['collectiveRepaymentSum'] += $value['collectiveRepaymentSum'];
            $sums['collectiveFutureSavingsSum'] += $value['collectiveFutureSavingsSum'];
            $sums['collectiveRevenueSum'] += $value['collectiveRevenueSum'];
            $sums['netCollectiveRepaymentSum'] += $value['netCollectiveRepaymentSum'];
            $sums['netCollectiveFutureSavingsSum'] += $value['netCollectiveFutureSavingsSum'];
            $sums['netCollectiveRevenueSum'] += $value['netCollectiveRevenueSum'];
        }

        $this->writeRow([
            'I alt',
            $processesLength,
            $this->formatNumberWithThousandSeparators($sums['collectiveRepaymentSum']),
            $this->formatNumberWithThousandSeparators($sums['collectiveFutureSavingsSum']),
            $this->formatNumberWithThousandSeparators($sums['collectiveRevenueSum']),
            $processesLength > 0 ? $this->formatNumberWithThousandSeparators($sums['collectiveRevenueSum'] / $processesLength) : '',
            $this->formatNumberWithThousandSeparators($sums['netCollectiveRepaymentSum']),
            $this->formatNumberWithThousandSeparators($sums['netCollectiveFutureSavingsSum']),
            $this->formatNumberWithThousandSeparators($sums['netCollectiveRevenueSum']),
            $processesLength > 0 ? $this->formatNumberWithThousandSeparators($sums['netCollectiveRevenueSum'] / $processesLength) : '',
        ]);
    }

    /**
     * Create a new array entry.
     *
     * @return array
     */
    private function newEntry()
    {
        return [
            'processes' => 0,
            'collectiveRepaymentSum' => 0.0,
            'collectiveFutureSavingsSum' => 0.0,
            'collectiveRevenueSum' => 0.0,
            'netCollectiveRepaymentSum' => 0.0,
            'netCollectiveFutureSavingsSum' => 0.0,
            'netCollectiveRevenueSum' => 0.0,
        ];
    }

    /**
     * @return Traversable
     *
     * @throws Exception
     */
    private function getProcesses(): Traversable
    {
        $queryBuilder = $this->entityManager->getRepository(Process::class)->createQueryBuilder('p')
            ->andWhere('p.originallyCompletedAt IS NOT NULL')
            ->join('p.processClient', 'client')
            ->andWhere('client.type = \'person\'');

        $startDate = $this->parameters['startdate'] ?? new \DateTime('2001-01-01');
        $endDate = $this->parameters['enddate'] ?? new \DateTime('2100-01-01');

        // We add one day to the endDate to make sure that processes
        // completed on the last day of a month is accounted for.
        $endDate->add(new \DateInterval('P1D'));

        $queryBuilder
            ->andWhere('p.originallyCompletedAt >= :startdate AND p.originallyCompletedAt < :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return SimpleBatchIteratorAggregate::fromQuery(
            $queryBuilder->getQuery(),
            100
        );
    }
}
