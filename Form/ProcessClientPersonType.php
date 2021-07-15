<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProcessClientPersonType.
 */
class ProcessClientPersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'label' => 'client.form.first_name',
            ])
            ->add('lastName', null, [
                'label' => 'client.form.last_name',
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
            ->add('numberOfChildren', null, [
                'label' => 'client.form.number_of_children',
            ])
            ->add('receivesPublicAid', ChoiceType::class, [
                'label' => 'client.form.receives_public_aid',
                'choices' => [
                    'common.empty_field_value' => null,
                    'common.boolean.Yes' => true,
                    'common.boolean.No' => false,
                ],
                'preferred_choices' => ['null'],
            ])
            ->add('employed', ChoiceType::class, [
                'label' => 'client.form.employed',
                'choices' => [
                    'common.empty_field_value' => null,
                    'common.boolean.Yes' => true,
                    'common.boolean.No' => false,
                ],
                'preferred_choices' => ['null'],
            ])
            ->add('hasOwnCompany', ChoiceType::class, [
                'label' => 'client.form.has_own_company',
                'choices' => [
                    'common.empty_field_value' => null,
                    'common.boolean.Yes' => true,
                    'common.boolean.No' => false,
                ],
                'preferred_choices' => ['null'],
            ])
            ->add('hasDriversLicense', ChoiceType::class, [
                'label' => 'client.form.has_drivers_license',
                'choices' => [
                    'common.empty_field_value' => null,
                    'common.boolean.Yes' => true,
                    'common.boolean.No' => false,
                ],
                'preferred_choices' => ['null'],
            ])
            ->add('hasCar', ChoiceType::class, [
                'label' => 'client.form.has_car',
                'choices' => [
                    'common.empty_field_value' => null,
                    'common.boolean.Yes' => true,
                    'common.boolean.No' => false,
                ],
                'preferred_choices' => ['null'],
            ])
            ->add('cars', CollectionType::class, [
                'entry_type' => CarType::class,
                'entry_options' => ['label' => false],
                'label' => 'client.form.cars',
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('companies', CollectionType::class, [
                'entry_type' => CompanyType::class,
                'entry_options' => ['label' => false],
                'label' => 'client.form.companies',
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
            'data_class' => ProcessClientPerson::class,
        ]);
    }
}
