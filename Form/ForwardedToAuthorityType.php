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
use Kontrolgruppen\CoreBundle\Entity\ForwardedToAuthority;
use Kontrolgruppen\CoreBundle\Form\Process\ClientTypesType;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ForwardedToAuthorityType.
 */
class ForwardedToAuthorityType extends AbstractType
{
    private $processTypeRepository;
    private $translator;

    /**
     * ServiceType constructor.
     *
     * @param ProcessTypeRepository $processTypeRepository
     * @param TranslatorInterface   $translator
     */
    public function __construct(ProcessTypeRepository $processTypeRepository, TranslatorInterface $translator)
    {
        $this->processTypeRepository = $processTypeRepository;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientTypes', ClientTypesType::class, [
                'label' => 'forwarded_to_authority.form.client_types.label',
                'help' => 'forwarded_to_authority.form.client_types.help',
            ]);

        $builder
            ->add('name', null, [
                'label' => 'service.form.name',
            ])
            ->add('processTypes', null, [
                'label' => 'forwarded_to_authority.form.process_types',
                'by_reference' => false,
                'attr' => ['class' => 'select2'],
                'choice_label' => function (AbstractTaxonomy $abstractTaxonomy) {
                    $label = $abstractTaxonomy->getName();

                    if ($clientTypes = $abstractTaxonomy->getClientTypes()) {
                        $label .= ' ('.implode(', ', array_map(function (string $clientType) {
                            return $this->translator->trans('process_client_type.'.$clientType);
                        }, $clientTypes)).')';
                    }

                    return $label;
                },
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ForwardedToAuthority::class,
        ]);
    }
}
