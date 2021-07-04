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
    protected $type = 'company';

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
}
