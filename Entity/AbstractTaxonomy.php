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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @UniqueEntity("name")
 */
abstract class AbstractTaxonomy extends AbstractEntity
{
    /**
     * @ORM\Column(type="json", nullable=true)
     *
     * @Groups("taxonomy_read")
     */
    protected $clientTypes;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups("taxonomy_read")
     *
     * @Gedmo\Versioned
     */
    protected $name;

    /**
     * @return array|null
     */
    public function getClientTypes(): ?array
    {
        return $this->clientTypes;
    }

    /**
     * @param array|null $clientTypes
     *
     * @return AbstractTaxonomy
     */
    public function setClientTypes(array $clientTypes = null): self
    {
        $this->clientTypes = $clientTypes;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AbstractTaxonomy
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName() ?? self::class;
    }
}
