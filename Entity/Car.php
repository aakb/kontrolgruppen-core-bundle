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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\CarRepository")
 *
 * @Gedmo\Loggable()
 */
class Car extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Gedmo\Versioned()
     */
    private $registrationNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $sharedOwnership;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient", inversedBy="cars")
     * @ORM\JoinColumn(nullable=false)
     */
    private $processClient;

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
    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    /**
     * @param string $registrationNumber
     *
     * @return Car
     */
    public function setRegistrationNumber(string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSharedOwnership(): ?bool
    {
        return $this->sharedOwnership;
    }

    /**
     * @param bool|null $sharedOwnership
     *
     * @return Car
     */
    public function setSharedOwnership(?bool $sharedOwnership): self
    {
        $this->sharedOwnership = $sharedOwnership;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     *
     * @return Car
     */
    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return AbstractProcessClient|null
     */
    public function getProcessClient(): ?AbstractProcessClient
    {
        return $this->processClient;
    }

    /**
     * @param AbstractProcessClient|null $processClient
     *
     * @return Car
     */
    public function setProcessClient(?AbstractProcessClient $processClient): self
    {
        $this->processClient = $processClient;

        return $this;
    }
}
