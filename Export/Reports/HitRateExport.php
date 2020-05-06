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
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;

/**
 * Class HitRateExport.
 */
class HitRateExport extends AbstractExport
{
    protected $title = 'Hitrate';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /**
     * HitRateExport constructor.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
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

        $this->sheet->setTitle($this->title);

        $this->writeHeader([
            'Kanal',
            'Antal afsluttede sager',
            'Vundne',
            'Hitrate',
        ]);

        $hitRate = [];

        foreach ($processes as $process) {
            if (null !== $process->getChannel()) {
                $channelName = $process->getChannel()->getName();
                if (!isset($hitRate[$channelName])) {
                    $hitRate[$channelName] = [
                        'processes' => 0,
                        'won' => 0,
                    ];
                }

                ++$hitRate[$channelName]['processes'];
                $hitRate[$channelName]['won'] += $process->getCourtDecision() ? 1 : 0;
            }
        }

        foreach ($hitRate as $key => $value) {
            $value['hitRate'] = $value['won'] / $value['processes'];

            $this->writeRow([
                $key,
                $this->formatNumber($value['processes']),
                $this->formatNumber($value['won']),
                $this->formatNumber($value['hitRate'] * 100).' %',
            ]);
        }
    }

    /**
     * @return Process[]
     *
     * @throws \Exception
     */
    private function getProcesses()
    {
        $queryBuilder = $this->entityManager->getRepository(Process::class)->createQueryBuilder('p')
            ->andWhere('p.completedAt IS NOT NULL');

        $startDate = $this->parameters['startdate'] ?? new \DateTime('2001-01-01');
        $endDate = $this->parameters['enddate'] ?? new \DateTime('2100-01-01');

        $queryBuilder
            ->andWhere('p.completedAt BETWEEN :startdate AND :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return $queryBuilder->getQuery()->execute();
    }
}
