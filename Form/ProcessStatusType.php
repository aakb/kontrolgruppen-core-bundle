<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\AbstractTaxonomy;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Form\Process\ClientTypesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProcessStatusType.
 */
class ProcessStatusType extends AbstractType
{
    private $translator;

    /**
     * ServiceType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientTypes', ClientTypesType::class, [
                'label' => 'process_status.form.client_types.label',
                'help' => 'process_status.form.client_types.help',
            ]);

        $builder
            ->add('name', null, [
                'label' => 'process_status.form.name',
            ])
            ->add('processTypes', null, [
                'label' => 'process_status.form.process_types',
                'by_reference' => false,
                'attr' => ['class' => 'select2'],
                'choice_label' => function (AbstractTaxonomy $taxonomy) {
                    $label = $taxonomy->getName();

                    if ($clientTypes = $taxonomy->getClientTypes()) {
                        $label .= ' ('.implode(', ', array_map(function (string $clientType) {
                            return $this->translator->trans('process_client_type.'.$clientType);
                        }, $clientTypes)).')';
                    }

                    return $label;
                },
            ])
            ->add('isForwardToAnotherAuthority', null, [
                'label' => 'process_status.form.is_forward_to_another_authority',
                'help' => 'process_status.form.is_forward_to_another_authority_help',
            ])
            ->add('isCompletingStatus', null, [
                'label' => 'process_status.form.is_completing_status',
                'help' => 'process_status.form.is_completing_status_help',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessStatus::class,
        ]);
    }
}
