<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\AbstractTaxonomy;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Event\GetConclusionTypesEvent;
use Kontrolgruppen\CoreBundle\Form\Process\ClientTypesType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProcessTypeType.
 */
class ProcessTypeType extends AbstractType
{
    private $dispatcher;
    private $entityManager;
    private $translator;

    /**
     * {@inheritdoc}
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = new GetConclusionTypesEvent();
        $this->dispatcher->dispatch(GetConclusionTypesEvent::NAME, $event);

        $conclusionTypes = $event->getTypes();

        $choices = [];

        foreach ($conclusionTypes as $conclusionType) {
            $choices[$conclusionType['name']] = $conclusionType['class'];
        }

        $builder
            ->add('clientTypes', ClientTypesType::class, [
                'label' => 'process_type.form.client_types.label',
                'help' => 'process_type.form.client_types.help',
            ]);

        $builder
            ->add('name', null, [
                'label' => 'process_type.form.name',
            ])
            ->add('processStatuses', null, [
                'label' => 'process_type.form.process_statuses',
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
            ->add('services', null, [
                'label' => 'process_type.form.services',
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
            ->add('channels', null, [
                'label' => 'process_type.form.channels',
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
            ->add('hideInDashboard', null, [
                'label' => 'process_type.form.hide_in_dashboard',
            ])
            ->add('conclusionClass', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'process_type.form.conclusion_class',
            ])
            ->add('defaultProcessStatus', EntityType::class, [
                'class' => ProcessStatus::class,
                'choice_label' => 'name',
                'label' => 'process_type.form.default_process_status',
                'help' => 'process_type.form.default_process_status_help',
            ])
            ->add('defaultProcessStatusOnEmptyCaseWorker', EntityType::class, [
                'class' => ProcessStatus::class,
                'choice_label' => 'name',
                'label' => 'process_type.form.default_process_status_on_empty_case_worker',
                'help' => 'process_type.form.default_process_status_on_empty_case_worker_help',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event) {
            $data = $event->getData();
            $clientTypes = $data['clientTypes'] ?? [];

            $taxonomyClass = ProcessStatus::class;
            $taxonomyField = 'processStatuses';

            $taxonomyRepository = $this->entityManager->getRepository($taxonomyClass);
            $allowedTaxonomies = $taxonomyRepository->findByClientTypes($clientTypes);
            $invalidTaxonomyIds = array_diff($data[$taxonomyField], array_keys($allowedTaxonomies));

            if (!empty($invalidTaxonomyIds)) {
                $invalidTaxonomies = $taxonomyRepository->findBy(['id' => $invalidTaxonomyIds]);
                foreach ($invalidTaxonomies as $taxonomy) {
                    $event->getForm()->addError(new FormError(
                        $this->translator->trans('%taxonomy_class% %taxonomy_name% is not valid for the selected client types', [
                            '%taxonomy_class%' => $this->translator->trans($taxonomyClass),
                            '%taxonomy_name%' => $taxonomy->getName(),
                        ])
                    ));
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProcessType::class,
        ]);
    }
}
