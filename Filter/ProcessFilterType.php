<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Filter;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProcessFilterType.
 */
class ProcessFilterType extends AbstractType
{
    private $processTypeRepository;
    private $processStatusRepository;
    private $userRepository;
    private $security;
    private $translator;

    /**
     * ProcessFilterType constructor.
     *
     * @param ProcessTypeRepository   $processTypeRepository
     * @param ProcessStatusRepository $processStatusRepository
     * @param UserRepository          $userRepository
     * @param Security                $security
     * @param TranslatorInterface     $translator
     */
    public function __construct(ProcessTypeRepository $processTypeRepository, ProcessStatusRepository $processStatusRepository, UserRepository $userRepository, Security $security, TranslatorInterface $translator)
    {
        $this->processTypeRepository = $processTypeRepository;
        $this->processStatusRepository = $processStatusRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('processClient', ProcessClientTypeFilterType::class, [
            'label' => false,
            // @see https://github.com/lexik/LexikFormFilterBundle/blob/master/Resources/doc/working-with-the-bundle.md#b-single-object
            'add_shared' => static function (FilterBuilderExecuterInterface $qbe) {
                $closure = static function (QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
                    $filterBuilder->leftJoin($alias.'.processClient', $joinAlias);
                };

                $qbe->addOnce($qbe->getAlias().'.processClient', 'i', $closure);
            },
        ]);

        $builder->add('processType', Filters\ChoiceFilterType::class, [
            'choices' => array_reduce($this->processTypeRepository->findAll(), function ($carry, $processType) {
                $carry[$processType->getName()] = $processType->getId();

                return $carry;
            }, []),
            'label' => 'process.form.process_type',
            'label_attr' => ['class' => 'sr-only'],
            'placeholder' => $this->translator->trans('process.table.filter.show_all_types'),
            'attr' => ['class' => 'form-control-sm ml-auto mr-3'],
            'choice_translation_domain' => false,
        ]);

        $builder->add('processStatus', Filters\ChoiceFilterType::class, [
            'choices' => array_reduce($this->processStatusRepository->findAll(), function ($carry, $processStatus) {
                $carry[$processStatus->getName()] = $processStatus->getId();

                return $carry;
            }, []),
            'label' => 'process.form.process_status',
            'label_attr' => ['class' => 'sr-only'],
            'placeholder' => $this->translator->trans('process.table.filter.show_all_status'),
            'attr' => ['class' => 'form-control-sm mr-3'],
            'choice_translation_domain' => false,
        ]);

        $builder->add('caseWorker', Filters\ChoiceFilterType::class, [
            'choices' => array_reduce($this->userRepository->findAll(), function ($carry, $caseWorker) {
                $carry[$caseWorker->getUsername()] = $caseWorker->getId();

                return $carry;
            }, []),
            'label' => 'process.form.case_worker',
            'label_attr' => ['class' => 'sr-only'],
            'placeholder' => $this->translator->trans('process.table.filter.show_all_case_worker'),
            'attr' => ['class' => 'form-control-sm mr-3'],
            'choice_translation_domain' => false,
        ]);

        $builder->add('completedAt', Filters\ChoiceFilterType::class, [
            'choices' => [
              $this->translator->trans('process.table.filter.status.open') => 'open',
              $this->translator->trans('process.table.filter.status.completed') => 'completed',
            ],
            'placeholder' => $this->translator->trans('process.table.filter.status.show_all'),
            'label' => 'process.form.status',
            'label_attr' => ['class' => 'sr-only'],
            'attr' => ['class' => 'form-control-sm'],
            'choice_translation_domain' => false,
            'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                if (empty($values['value'])) {
                    return null;
                }

                $expression = $filterQuery->getExpr();

                return ('open' === $values['value'])
                    ? $filterQuery->createCondition($expression->isNull($field))
                    : $filterQuery->createCondition($expression->isNotNull($field));
            },
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'process_filter';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'validation_groups' => ['filtering'],
            ]
        );
    }
}
