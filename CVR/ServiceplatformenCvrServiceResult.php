<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class ServiceplatformenCvrServiceResult.
 */
class ServiceplatformenCvrServiceResult implements CvrServiceResultInterface
{
    private $response;
    private $propertyAccessor;

    /**
     * ServiceplatformenCvrServiceResult constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->getProperty('GetLegalUnitResponse.LegalUnit.LegalUnitName.name');
    }

    /**
     * {@inheritdoc}
     */
    public function getStreetName(): string
    {
        return $this->getProperty('GetLegalUnitResponse.LegalUnit.AddressOfficial.AddressPostalExtended.StreetName');
    }

    /**
     * {@inheritdoc}
     */
    public function getHouseNumber(): string
    {
        return $this->getProperty('GetLegalUnitResponse.LegalUnit.AddressOfficial.AddressPostalExtended.StreetBuildingIdentifier');
    }

    /**
     * {@inheritdoc}
     */
    public function getPostalCode(): string
    {
        return $this->getProperty('GetLegalUnitResponse.LegalUnit.AddressOfficial.AddressPostalExtended.PostCodeIdentifier');
    }

    /**
     * {@inheritdoc}
     */
    public function getCity(): string
    {
        return $this->getProperty('GetLegalUnitResponse.LegalUnit.AddressOfficial.AddressPostalExtended.DistrictName');
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress(): string
    {
        $address = $this->getStreetName();

        $address .= null !== $this->getHouseNumber()
            ? ' '.$this->getHouseNumber()
            : ''
        ;

        return $address;
    }

    /**
     * Returns the value of the property if it exists otherwise it returns an empty string.
     *
     * @param string $property name of property
     *
     * @return string
     */
    private function getProperty(string $property): string
    {
        return $this->propertyAccessor->isReadable($this->response, $property)
            ? $this->propertyAccessor->getValue($this->response, $property)
            : ''
        ;
    }
}
