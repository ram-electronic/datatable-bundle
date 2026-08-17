<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Form;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterConditionFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Reusable form type for a single filter condition.
 * Represents one row in the dynamic filter builder.
 * Field definitions are passed via options for reusability.
 * Renders as a LiveComponent for dynamic operator filtering.
 *
 * @extends AbstractType<FilterConditionFormData>
 */
class FilterConditionType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FilterFieldRegistry $registry */
        $registry = $options['field_registry'];

        $builder
            ->add('field', ChoiceType::class, [
                'label' => 'label.select_field',
                'required' => false,
                'choices' => $registry->getFieldChoices(),
                'placeholder' => 'label.select_field',
            ])
            ->add('operator', EnumType::class, [
                'class' => FilterOperator::class,
                'label' => 'label.select_operator',
                'required' => false,
                'placeholder' => 'label.select_operator',
            ])
            ->add('value', TextType::class, [
                'label' => 'label.filter_value',
                'required' => false,
                'attr' => [
                    'placeholder' => 'label.filter_value',
                ],
            ])
        ;
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        // Pass field registry to the view for LiveComponent rendering
        $view->vars['field_registry'] = $options['field_registry'];
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'label.filter_condition',
            'field_registry' => null,
            'data_class' => FilterConditionFormData::class,
        ]);

        $resolver->setRequired('field_registry');
        $resolver->setAllowedTypes('field_registry', FilterFieldRegistry::class);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'filter_condition';
    }
}
