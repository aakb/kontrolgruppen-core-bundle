<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

/**
 * Class Cvr.
 */
class Cvr
{
    private $cvr;

    /**
     * Cvr constructor.
     *
     * @param string $cvr
     */
    public function __construct(string $cvr)
    {
        $this->setCvr($cvr);
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->cvr;
    }

    /**
     * @param string $cvr
     */
    private function setCvr(string $cvr)
    {
        // Remove anything that's not a decimal digit.
        $cvr = preg_replace('/[^\d]+/', '', $cvr);

        if (!preg_match('/^\d{8}$/', $cvr)) {
            throw new \InvalidArgumentException('$cvr must contain exactly 8 digits');
        }

        $this->cvr = $cvr;
    }
}
