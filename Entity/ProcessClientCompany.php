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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessClientCompanyRepository")
 *
 * @Gedmo\Loggable()
 */
class ProcessClientCompany extends AbstractProcessClient
{
    public const TYPE = parent::COMPANY;
    protected $type = parent::COMPANY;

    /**
     * @ORM\Embedded(class="Kontrolgruppen\CoreBundle\Entity\ContactPerson")
     */
    private $contactPerson;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->contactPerson = new ContactPerson();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getCvr() ?? parent::__toString();
    }

    /**
     * Get cvr.
     *
     * @return string|null
     */
    public function getCvr(): ?string
    {
        return $this->getIdentifier();
    }

    /**
     * Set svr.
     *
     * @param string $cvr
     *
     * @return $this
     */
    public function setCvr(string $cvr): self
    {
        return $this->setIdentifier($cvr);
    }

    /**
     * @return ContactPerson
     */
    public function getContactPerson(): ContactPerson
    {
        return $this->contactPerson;
    }

    /**
     * @param ContactPerson $contactPerson
     *
     * @return $this
     */
    public function setContactPerson(ContactPerson $contactPerson): self
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }
}
