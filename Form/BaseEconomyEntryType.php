<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\BaseEconomyEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseEconomyEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', HiddenType::class)
            ->add('process', HiddenType::class)
            ->add('text', null, [
                'label' => 'economy_entry.form.base.text',
            ])
            ->add('date', null, [
                'label' => 'economy_entry.form.base.date',
            ])
            ->add('amount', null, [
                'label' => 'economy_entry.form.base.amount',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BaseEconomyEntry::class,
        ]);
    }
}
