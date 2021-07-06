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
use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Form\Process\ClientTypesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ServiceType.
 */
class ServiceType extends AbstractType
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
                'label' => 'service.form.client_types.label',
                'help' => 'service.form.client_types.help',
            ]);

        $builder
            ->add('name', null, [
                'label' => 'service.form.name',
            ])
            ->add('netDefaultValue', PercentType::class, [
                'label' => 'service.form.net_default_value',
                'scale' => 2,
            ])
            ->add('processTypes', null, [
                'label' => 'service.form.process_types',
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
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
