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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository")
 *
 * @Gedmo\Loggable()
 */
class ProcessStatus extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="processStatus")
     */
    private $processes;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessType", mappedBy="processStatuses")
     *
     * @ValidClientTaxonomy()
     */
    private $processTypes;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Gedmo\Versioned
     */
    private $isForwardToAnotherAuthority = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Gedmo\Versioned
     */
    private $isCompletingStatus = false;

    /**
     * ProcessStatus constructor.
     */
    public function __construct()
    {
        $this->processes = new ArrayCollection();
        $this->processTypes = new ArrayCollection();
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
     * @return $this
     */
    public function addProcess(Process $process): self
    {
        if (!$this->processes->contains($process)) {
            $this->processes[] = $process;
            $process->setProcessStatus($this);
        }

        return $this;
    }

    /**
     * @param Process $process
     *
     * @return $this
     */
    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getProcessStatus() === $this) {
                $process->setProcessStatus(null);
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
     * @return $this
     */
    public function addProcessType(ProcessType $processType): self
    {
        if (!$this->processTypes->contains($processType)) {
            $this->processTypes[] = $processType;
            $processType->addProcessStatus($this);
        }

        return $this;
    }

    /**
     * @param ProcessType $processType
     *
     * @return $this
     */
    public function removeProcessType(ProcessType $processType): self
    {
        if ($this->processTypes->contains($processType)) {
            $this->processTypes->removeElement($processType);
            $processType->removeProcessStatus($this);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsForwardToAnotherAuthority(): bool
    {
        return $this->isForwardToAnotherAuthority;
    }

    /**
     * @param bool $isForwardToAnotherAuthority
     *
     * @return $this
     */
    public function setIsForwardToAnotherAuthority(bool $isForwardToAnotherAuthority): self
    {
        $this->isForwardToAnotherAuthority = $isForwardToAnotherAuthority;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCompletingStatus(): bool
    {
        return $this->isCompletingStatus;
    }

    /**
     * @param bool $isCompletingStatus
     *
     * @return $this
     */
    public function setIsCompletingStatus(bool $isCompletingStatus): self
    {
        $this->isCompletingStatus = $isCompletingStatus;

        return $this;
    }
}
