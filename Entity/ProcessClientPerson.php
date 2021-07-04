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

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessClientPersonRepository")
 *
 * @Gedmo\Loggable()
 */
class ProcessClientPerson extends AbstractProcessClient
{
    protected $type = 'person';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $numberOfChildren;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $receivesPublicAid;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $employed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $hasOwnCompany;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Company", mappedBy="processClient", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $companies;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasDriversLicense;

    /**
     * ProcessClientPerson constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->companies = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getCpr() ?? parent::__toString();
    }

    /**
     * Get cpr.
     *
     * @return string|null
     */
    public function getCpr(): ?string
    {
        return $this->getIdentifier();
    }

    /**
     * Set cpr.
     *
     * @param string $cpr
     *
     * @return $this
     */
    public function setCpr(string $cpr): self
    {
        return $this->setIdentifier($cpr);
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this->updateName();
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this->updateName();
    }

    /**
     * @return int|null
     */
    public function getNumberOfChildren(): ?int
    {
        return $this->numberOfChildren;
    }

    /**
     * @param int $numberOfChildren
     *
     * @return $this
     */
    public function setNumberOfChildren(int $numberOfChildren): self
    {
        $this->numberOfChildren = $numberOfChildren;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getReceivesPublicAid(): ?bool
    {
        return $this->receivesPublicAid;
    }

    /**
     * @param bool $receivesPublicAid
     *
     * @return $this
     */
    public function setReceivesPublicAid(bool $receivesPublicAid): self
    {
        $this->receivesPublicAid = $receivesPublicAid;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEmployed(): ?bool
    {
        return $this->employed;
    }

    /**
     * @param bool $employed
     *
     * @return $this
     */
    public function setEmployed(bool $employed): self
    {
        $this->employed = $employed;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasOwnCompany(): ?bool
    {
        return $this->hasOwnCompany;
    }

    /**
     * @param bool $hasOwnCompany
     *
     * @return $this
     */
    public function setHasOwnCompany(bool $hasOwnCompany): self
    {
        $this->hasOwnCompany = $hasOwnCompany;

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @param Company $company
     *
     * @return $this
     */
    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setProcessClient($this);
        }

        return $this;
    }

    /**
     * @param Company $company
     *
     * @return $this
     */
    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            // set the owning side to null (unless already changed)
            if ($company->getProcessClient() === $this) {
                $company->setProcessClient(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasDriversLicense(): ?bool
    {
        return $this->hasDriversLicense;
    }

    /**
     * @param bool $hasDriversLicense
     *
     * @return $this
     */
    public function setHasDriversLicense(bool $hasDriversLicense): self
    {
        $this->hasDriversLicense = $hasDriversLicense;

        return $this;
    }

    /**
     * Build and update name from first and last name.
     *
     * @return $this
     */
    private function updateName(): self
    {
        $this->setName(trim($this->getFirstName().' '.$this->getLastName()));

        return $this;
    }
}
