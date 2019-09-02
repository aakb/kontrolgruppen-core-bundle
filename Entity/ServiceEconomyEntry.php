<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @Gedmo\Loggable()
 */
class ServiceEconomyEntry extends EconomyEntry
{
    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Service")
     * @ORM\JoinColumn(nullable=true)
     */
    private $service;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $periodFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned()
     */
    private $periodTo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned()
     *
     * How many months does the amount cover. Defaults to 1.
     */
    private $amountPeriod;

    /**
     * @ORM\Column(type="float")
     */
    private $futureSavingsAmount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $futureSavingsPeriodFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $futureSavingsPeriodTo;

    /**
     * @ORM\Column(type="float")
     */
    private $repaymentAmount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $repaymentPeriodFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $repaymentPeriodTo;

    public function __construct()
    {
        $this->futureSavingsAmount = 0;
        $this->futureSavingsPeriodFrom = new \DateTime();
        $this->futureSavingsPeriodTo = new \DateTime();
        $this->repaymentAmount = 0;
        $this->repaymentPeriodFrom = new \DateTime();
        $this->repaymentPeriodTo = new \DateTime();
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPeriodFrom(): ?\DateTime
    {
        return $this->periodFrom;
    }

    public function setPeriodFrom(?\DateTime $periodFrom): self
    {
        $this->periodFrom = $periodFrom;

        return $this;
    }

    public function getPeriodTo(): ?\DateTime
    {
        return $this->periodTo;
    }

    public function setPeriodTo(?\DateTime $periodTo): self
    {
        $this->periodTo = $periodTo;

        return $this;
    }

    public function getAmountPeriod(): ?int
    {
        return $this->amountPeriod;
    }

    public function setAmountPeriod(?int $amountPeriod): self
    {
        $this->amountPeriod = $amountPeriod;

        return $this;
    }

    public function getFutureSavingsAmount(): ?float
    {
        return $this->futureSavingsAmount;
    }

    public function setFutureSavingsAmount(float $futureSavingsAmount): self
    {
        $this->futureSavingsAmount = $futureSavingsAmount;

        return $this;
    }

    public function getFutureSavingsPeriodFrom(): ?\DateTimeInterface
    {
        return $this->futureSavingsPeriodFrom;
    }

    public function setFutureSavingsPeriodFrom(\DateTimeInterface $futureSavingsPeriodFrom): self
    {
        $this->futureSavingsPeriodFrom = $futureSavingsPeriodFrom;

        return $this;
    }

    public function getFutureSavingsPeriodTo(): ?\DateTimeInterface
    {
        return $this->futureSavingsPeriodTo;
    }

    public function setFutureSavingsPeriodTo(\DateTimeInterface $futureSavingsPeriodTo): self
    {
        $this->futureSavingsPeriodTo = $futureSavingsPeriodTo;

        return $this;
    }

    public function getRepaymentAmount(): ?float
    {
        return $this->repaymentAmount;
    }

    public function setRepaymentAmount(float $repaymentAmount): self
    {
        $this->repaymentAmount = $repaymentAmount;

        return $this;
    }

    public function getRepaymentPeriodFrom(): ?\DateTimeInterface
    {
        return $this->repaymentPeriodFrom;
    }

    public function setRepaymentPeriodFrom(\DateTimeInterface $repaymentPeriodFrom): self
    {
        $this->repaymentPeriodFrom = $repaymentPeriodFrom;

        return $this;
    }

    public function getRepaymentPeriodTo(): ?\DateTimeInterface
    {
        return $this->repaymentPeriodTo;
    }

    public function setRepaymentPeriodTo(\DateTimeInterface $repaymentPeriodTo): self
    {
        $this->repaymentPeriodTo = $repaymentPeriodTo;

        return $this;
    }
}
