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
 * @ORM\Entity
 */
class WeightedConclusion extends Conclusion
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $argumentsFor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $argumentsAgainst;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $conclusion;

    public function getArgumentsFor(): ?string
    {
        return $this->argumentsFor;
    }

    public function setArgumentsFor(?string $argumentsFor): self
    {
        $this->argumentsFor = $argumentsFor;

        return $this;
    }

    public function getArgumentsAgainst(): ?string
    {
        return $this->argumentsAgainst;
    }

    public function setArgumentsAgainst(?string $argumentsAgainst): self
    {
        $this->argumentsAgainst = $argumentsAgainst;

        return $this;
    }

    public function getConclusion(): ?string
    {
        return $this->conclusion;
    }

    public function setConclusion(?string $conclusion): self
    {
        $this->conclusion = $conclusion;

        return $this;
    }
}