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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ForwardedToAuthorityRepository")
 *
 * @Gedmo\Loggable()
 */
class ForwardedToAuthority extends AbstractTaxonomy
{
    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="forwardedToAuthorities")
     */
    private $processes;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessType", inversedBy="forwardedToAuthorities")
     *
     * @ValidClientTaxonomy()
     */
    private $processTypes;

    /**
     * ForwardedToAuthority constructor.
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
        }

        return $this;
    }
}
