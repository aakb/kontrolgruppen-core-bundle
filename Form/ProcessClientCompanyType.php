<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProcessClientCompanyType.
 */
class ProcessClientCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'client.form.name',
            ])
            ->add('address', null, [
                'label' => 'client.form.address',
            ])
            ->add('postalCode', null, [
                'label' => 'client.form.postal_code',
            ])
            ->add('city', null, [
                'label' => 'client.form.city',
            ])
            ->add('telephone', null, [
                'label' => 'client.form.telephone',
            ])
            ->add('contactPerson', ContactPersonType::class, [
                'label' => 'client.form.contact_person',
            ])
            ->add('cars', CollectionType::class, [
                'entry_type' => CarType::class,
                'entry_options' => ['label' => false],
                'label' => 'client.form.cars',
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessClientCompany::class,
        ]);
    }
}
