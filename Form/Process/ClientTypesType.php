<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form\Process;

use Kontrolgruppen\CoreBundle\Service\ProcessClientManager;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ClientTypeType.
 */
class ClientTypesType extends ChoiceType
{
    /**
     * @var ProcessClientManager
     */
    private $processClientManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ClientTypeType constructor.
     *
     * @param ChoiceListFactoryInterface|null $choiceListFactory
     * @param ProcessClientManager            $processClientManager
     * @param TranslatorInterface             $translator
     */
    public function __construct(ChoiceListFactoryInterface $choiceListFactory, ProcessClientManager $processClientManager, TranslatorInterface $translator)
    {
        parent::__construct($choiceListFactory);
        $this->processClientManager = $processClientManager;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $clientTypes = $this->processClientManager->getClientTypes();
        $choices = [];
        foreach (array_keys($clientTypes) as $name) {
            $choices[$this->translator->trans('process_client_type.'.$name)] = $name;
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true,
        ]);
    }
}
