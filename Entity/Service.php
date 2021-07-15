<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kontrolgruppen\CoreBundle\Validator\ValidClientTaxonomy;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ServiceRepository")
 *
 * @Gedmo\Loggable()
 */
class Service extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="service")
     */
    private $processes;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessType", mappedBy="services")
     *
     * @ValidClientTaxonomy()
     */
    private $processTypes;

    /**
     * @ORM\Column(type="float")
     */
    private $netDefaultValue;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\LockedNetValue", mappedBy="service")
     */
    private $lockedNetValues;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\RevenueEntry", mappedBy="service", orphanRemoval=true)
     */
    private $revenueEntries;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->processes = new ArrayCollection();
        $this->processTypes = new ArrayCollection();
        $this->lockedNetValues = new ArrayCollection();
        $this->revenueEntries = new ArrayCollection();
    }

    /**
     * @return Collection|Process[]
     */
    public function getProcesses(): Collection
    {
        return $this->processes;
    }

    /**
     * @param Process $process
     *
     * @return Service
     */
    public function addProcess(Process $process): self
    {
        if (!$this->processes->contains($process)) {
            $this->processes[] = $process;
            $process->setService($this);
        }

        return $this;
    }

    /**
     * @param Process $process
     *
     * @return Service
     */
    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getService() === $this) {
                $process->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProcessType[]
     */
    public function getProcessTypes(): Collection
    {
        return $this->processTypes;
    }

    /**
     * @param ProcessType $processType
     *
     * @return Service
     */
    public function addProcessType(ProcessType $processType): self
    {
        if (!$this->processTypes->contains($processType)) {
            $this->processTypes[] = $processType;
            $processType->addService($this);
        }

        return $this;
    }

    /**
     * @param ProcessType $processType
     *
     * @return Service
     */
    public function removeProcessType(ProcessType $processType): self
    {
        if ($this->processTypes->contains($processType)) {
            $this->processTypes->removeElement($processType);
            $processType->removeService($this);
        }

        return $this;
    }

    /**
     * @return float|null
     */
    public function getNetDefaultValue(): ?float
    {
        return $this->netDefaultValue;
    }

    /**
     * @param float $netDefaultValue
     *
     * @return Service
     */
    public function setNetDefaultValue(float $netDefaultValue): self
    {
        $this->netDefaultValue = $netDefaultValue;

        return $this;
    }

    /**
     * @return Collection|LockedNetValue[]
     */
    public function getLockedNetValues(): Collection
    {
        return $this->lockedNetValues;
    }

    /**
     * @param LockedNetValue $lockedNetValue
     *
     * @return Service
     */
    public function addLockedNetValue(LockedNetValue $lockedNetValue): self
    {
        if (!$this->lockedNetValues->contains($lockedNetValue)) {
            $this->lockedNetValues[] = $lockedNetValue;
            $lockedNetValue->setService($this);
        }

        return $this;
    }

    /**
     * @param LockedNetValue $lockedNetValue
     *
     * @return Service
     */
    public function removeLockedNetValue(LockedNetValue $lockedNetValue): self
    {
        if ($this->lockedNetValues->contains($lockedNetValue)) {
            $this->lockedNetValues->removeElement($lockedNetValue);
            // set the owning side to null (unless already changed)
            if ($lockedNetValue->getService() === $this) {
                $lockedNetValue->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RevenueEntry[]
     */
    public function getRevenueEntries(): Collection
    {
        return $this->revenueEntries;
    }

    /**
     * @param RevenueEntry $revenueEntry
     *
     * @return Service
     */
    public function addRevenueEntry(RevenueEntry $revenueEntry): self
    {
        if (!$this->revenueEntries->contains($revenueEntry)) {
            $this->revenueEntries[] = $revenueEntry;
            $revenueEntry->setService($this);
        }

        return $this;
    }

    /**
     * @param RevenueEntry $revenueEntry
     *
     * @return Service
     */
    public function removeRevenueEntry(RevenueEntry $revenueEntry): self
    {
        if ($this->revenueEntries->contains($revenueEntry)) {
            $this->revenueEntries->removeElement($revenueEntry);
            // set the owning side to null (unless already changed)
            if ($revenueEntry->getService() === $this) {
                $revenueEntry->setService(null);
            }
        }

        return $this;
    }
}
