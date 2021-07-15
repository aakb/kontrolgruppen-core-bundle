<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FaellesSQLCprServiceResult.
 */
class FaellesSQLCprServiceResult implements CprServiceResultInterface
{
    private $serviceResult;

    /**
     * FaellesSQLCprServiceResult constructor.
     *
     * @param array $serviceResult
     */
    public function __construct(array $serviceResult)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        $this->serviceResult = $optionsResolver->resolve($serviceResult);
    }

    /**
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefined([
            'CPR',
            'Bynavn',
            'Mellemnavn',
            'PostnummerOgBy',
            'Adresseringsadresse',
            'Adresseringsnavn',
        ]);

        $optionsResolver->setRequired([
            'Fornavn',
            'Efternavn',
            'Vejnavn',
            'HusNr',
            'Etage',
            'Side',
            'Postnummer',
            'Postdistrikt',
        ]);

        $optionsResolver->setAllowedTypes('Fornavn', 'string');
        $optionsResolver->setAllowedTypes('Efternavn', 'string');
        $optionsResolver->setAllowedTypes('Vejnavn', 'string');
        $optionsResolver->setAllowedTypes('HusNr', ['null', 'string']);
        $optionsResolver->setAllowedTypes('Etage', ['null', 'string']);
        $optionsResolver->setAllowedTypes('Side', ['null', 'string']);
        $optionsResolver->setAllowedTypes('Postnummer', 'string');
        $optionsResolver->setAllowedTypes('Postdistrikt', 'string');
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->serviceResult['Fornavn'];
    }

    /**
     * {@inheritdoc}
     */
    public function getMiddleName(): ?string
    {
        return $this->serviceResult['Mellemnavn'];
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName(): string
    {
        return $this->serviceResult['Efternavn'];
    }

    /**
     * @return string
     */
    public function getStreetName(): string
    {
        return $this->serviceResult['Vejnavn'];
    }

    /**
     * {@inheritdoc}
     */
    public function getHouseNumber(): ?string
    {
        return $this->serviceResult['HusNr'];
    }

    /**
     * @return string|null
     */
    public function getFloor(): ?string
    {
        return $this->serviceResult['Etage'];
    }

    /**
     * @return string|null
     */
    public function getSide(): ?string
    {
        return $this->serviceResult['Side'];
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->serviceResult['Postnummer'];
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->serviceResult['Postdistrikt'];
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

        $address .= null !== $this->getFloor()
            ? ' '.$this->getFloor()
            : ''
        ;

        $address .= null !== $this->getSide()
            ? ' '.$this->getSide()
            : ''
        ;

        return $address;
    }
}
