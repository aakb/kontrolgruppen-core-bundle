<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Filter;

use Kontrolgruppen\CoreBundle\Service\ProcessClientManager;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProcessClientTypeFilterType.
 */
class ProcessClientTypeFilterType extends AbstractType
{
    private $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', Filters\ChoiceFilterType::class, [
            'choices' => array_flip(ProcessClientManager::getClientTypes()),
            'label' => 'process.table.form.client_type',
            'label_attr' => ['class' => 'sr-only'],
            'placeholder' => $this->translator->trans('process.table.filter.show_all_client_types'),
            'attr' => ['class' => 'form-control-sm ml-auto mr-3'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'client_process_filter';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Filters\SharedableFilterType::class; // this allow us to use the "add_shared" option
    }
}
