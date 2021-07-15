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

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CVR;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson", inversedBy="companies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $processClient;

    /**
     * @ORM\Column(type="boolean")
     */
    private $highlighted;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getCVR(): ?string
    {
        return $this->CVR;
    }

    /**
     * @param string $cvr
     *
     * @return $this
     */
    public function setCVR(string $cvr): self
    {
        $this->CVR = $cvr;

        return $this;
    }

    /**
     * @return ProcessClientPerson|null
     */
    public function getProcessClient(): ?ProcessClientPerson
    {
        return $this->processClient;
    }

    /**
     * @param ProcessClientPerson|null $processClient
     *
     * @return $this
     */
    public function setProcessClient(?ProcessClientPerson $processClient): self
    {
        $this->processClient = $processClient;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isHighlighted(): ?bool
    {
        return $this->highlighted;
    }

    /**
     * @param bool $highlighted
     *
     * @return $this
     */
    public function setHighlighted(bool $highlighted): self
    {
        $this->highlighted = $highlighted;

        return $this;
    }
}
