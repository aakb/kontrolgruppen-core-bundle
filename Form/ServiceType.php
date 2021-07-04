<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Form\Process\ClientTypesType;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ServiceType.
 */
class ServiceType extends AbstractType
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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientTypes', ClientTypesType::class, [
                'label' => 'process_type.form.client_types',
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
                'choice_label' => function (ProcessType $processType) {
                    $label = $processType->getName();

                    if ($clientTypes = $processType->getClientTypes()) {
                        $label .= ' ('.implode(', ', array_map(function (string $clientType) {
                            return $this->translator->trans('process_client_type.'.$clientType);
                        }, $clientTypes)).')';
                    }

                    return $label;
                },
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event) {
            $data = $event->getData();
            $clientTypes = $data['clientTypes'] ?? [];
            $allowedProcessTypes = $this->processTypeRepository->findByClientTypes($clientTypes);
            $invalidProcessTypeIds = array_diff($data['processTypes'], array_keys($allowedProcessTypes));

            if (!empty($invalidProcessTypeIds)) {
                $invalidProcessTypes = $this->processTypeRepository->findBy(['id' => $invalidProcessTypeIds]);
                foreach ($invalidProcessTypes as $processType) {
                    $event->getForm()->addError(new FormError(
                        $this->translator->trans('Process type %process_type_name% is not valid for the selected client types', [
                            '%process_type_name%' => $processType->getName(),
                        ])
                    ));
                }
            }
        });
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
