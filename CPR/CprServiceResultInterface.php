<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

/**
 * Interface CprServiceResult.
 */
interface CprServiceResultInterface
{
    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Get middle name.
     *
     * @return string|null
     */
    public function getMiddleName(): ?string;

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string;

    /**
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
     * @return string|null
     */
    public function getFloor(): ?string;

    /**
     * @return string|null
     */
    public function getSide(): ?string;

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
