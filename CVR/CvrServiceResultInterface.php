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
 * Interface CvrServiceResult.
 */
interface CvrServiceResultInterface
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get street name.
     *
     * @return string
     */
    public function getStreetName(): string;

    /**
     * Get house number.
     *
     * @return string|null
     */
    public function getHouseNumber(): ?string;

    /**
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * Get formatted street address.
     *
     * @return string
     */
    public function getAddress(): string;
}
