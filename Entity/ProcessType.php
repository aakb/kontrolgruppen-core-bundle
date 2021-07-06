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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository")
 *
 * @Gedmo\Loggable()
 */
class ProcessType extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="processType")
     */
    private $processes;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus", inversedBy="processTypes")
     *
     * @ValidClientTaxonomy()
     */
    private $processStatuses;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $conclusionClass;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hideInDashboard;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Service", inversedBy="processTypes")
     *
     * @ValidClientTaxonomy()
     */
    private $services;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Channel", inversedBy="processTypes")
     *
     * @ValidClientTaxonomy()
     */
    private $channels;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus")
     * @ORM\JoinColumn(nullable=false)
     *
     * @ValidClientTaxonomy()
     */
    private $defaultProcessStatus;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus")
     * @ORM\JoinColumn(nullable=false)
     *
     * @ValidClientTaxonomy()
     */
    private $defaultProcessStatusOnEmptyCaseWorker;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ForwardedToAuthority", mappedBy="processTypes")
     */
    private $forwardedToAuthorities;

    /**
     * ProcessType constructor.
     */
    public function __construct()
    {
        $this->processes = new ArrayCollection();
        $this->processStatuses = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->forwardedToAuthorities = new ArrayCollection();
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
            $process->setProcessType($this);
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
            if ($process->getProcessType() === $this) {
                $process->setProcessType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProcessStatus[]
     */
    public function getProcessStatuses(): Collection
    {
        return $this->processStatuses;
    }

    /**
     * @param ProcessStatus $processStatus
     *
     * @return $this
     */
    public function addProcessStatus(ProcessStatus $processStatus): self
    {
        if (!$this->processStatuses->contains($processStatus)) {
            $this->processStatuses[] = $processStatus;
        }

        return $this;
    }

    /**
     * @param ProcessStatus $processStatus
     *
     * @return $this
     */
    public function removeProcessStatus(ProcessStatus $processStatus): self
    {
        if ($this->processStatuses->contains($processStatus)) {
            $this->processStatuses->removeElement($processStatus);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConclusionClass(): ?string
    {
        return $this->conclusionClass;
    }

    /**
     * @param string $conclusionClass
     *
     * @return $this
     */
    public function setConclusionClass(string $conclusionClass): self
    {
        $this->conclusionClass = $conclusionClass;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHideInDashboard(): ?bool
    {
        return $this->hideInDashboard;
    }

    /**
     * @param bool|null $hideInDashboard
     *
     * @return $this
     */
    public function setHideInDashboard(?bool $hideInDashboard): self
    {
        $this->hideInDashboard = $hideInDashboard;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    /**
     * @param Service $service
     *
     * @return $this
     */
    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    /**
     * @param Service $service
     *
     * @return $this
     */
    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
        }

        return $this;
    }

    /**
     * @return Collection|Channel[]
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * @param Channel $channel
     *
     * @return $this
     */
    public function addChannel(Channel $channel): self
    {
        if (!$this->channels->contains($channel)) {
            $this->channels[] = $channel;
        }

        return $this;
    }

    /**
     * @param Channel $channel
     *
     * @return $this
     */
    public function removeChannel(Channel $channel): self
    {
        if ($this->channels->contains($channel)) {
            $this->channels->removeElement($channel);
        }

        return $this;
    }

    /**
     * @return ProcessStatus|null
     */
    public function getDefaultProcessStatus(): ?ProcessStatus
    {
        return $this->defaultProcessStatus;
    }

    /**
     * @param ProcessStatus|null $defaultProcessStatus
     *
     * @return $this
     */
    public function setDefaultProcessStatus(?ProcessStatus $defaultProcessStatus): self
    {
        $this->defaultProcessStatus = $defaultProcessStatus;

        return $this;
    }

    /**
     * @return ProcessStatus|null
     */
    public function getDefaultProcessStatusOnEmptyCaseWorker(): ?ProcessStatus
    {
        return $this->defaultProcessStatusOnEmptyCaseWorker;
    }

    /**
     * @param ProcessStatus|null $defaultProcessStatusOnEmptyCaseWorker
     *
     * @return $this
     */
    public function setDefaultProcessStatusOnEmptyCaseWorker(?ProcessStatus $defaultProcessStatusOnEmptyCaseWorker): self
    {
        $this->defaultProcessStatusOnEmptyCaseWorker = $defaultProcessStatusOnEmptyCaseWorker;

        return $this;
    }

    /**
     * @return Collection|ForwardedToAuthority[]
     */
    public function getForwardedToAuthorities(): Collection
    {
        return $this->forwardedToAuthorities;
    }

    /**
     * @param ForwardedToAuthority $forwardedToAuthority
     *
     * @return ProcessType
     */
    public function addForwardedToAuthority(ForwardedToAuthority $forwardedToAuthority): self
    {
        if (!$this->forwardedToAuthorities->contains($forwardedToAuthority)) {
            $this->forwardedToAuthorities[] = $forwardedToAuthority;
            $forwardedToAuthority->addProcessType($this);
        }

        return $this;
    }

    /**
     * @param ForwardedToAuthority $forwardedToAuthority
     *
     * @return ProcessType
     */
    public function removeForwardedToAuthority(ForwardedToAuthority $forwardedToAuthority): self
    {
        if ($this->forwardedToAuthorities->contains($forwardedToAuthority)) {
            $this->forwardedToAuthorities->removeElement($forwardedToAuthority);
            $forwardedToAuthority->removeProcessType($this);
        }

        return $this;
    }
}
