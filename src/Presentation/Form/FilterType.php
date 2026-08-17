<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Form;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

/**
 * Generic form type for table filtering.
 * Uses GET method to enable URL-shareable filter state.
 * Supports dynamic operator-based filters with LiveCollectionType.
 *
 * @extends AbstractType<FilterFormData>
 */
class FilterType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FilterFieldRegistry $fieldRegistry */
        $fieldRegistry = $options['field_registry'];

        $builder
            ->add('search', TextType::class, [
                'required' => false,
                'label' => 'label.search',
                'attr' => [
                    'placeholder' => 'placeholder.search',
                ],
            ])
            ->add('sort', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                ],
            ])
            ->add('direction', EnumType::class, [
                'class' => SortDirection::class,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                ],
            ])
            ->add('page', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                ],
            ])
            ->add('filters', LiveCollectionType::class, [
                'entry_type' => FilterConditionType::class,
                'entry_options' => [
                    'label' => false,
                    'field_registry' => $fieldRegistry,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'button_add_options' => [
                    'label' => 'action.add_filter',
                ],
                'button_delete_options' => [
                    'label' => 'action.remove_filter',
                ],
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'data_class' => FilterFormData::class,
            'field_registry' => null,
        ]);

        $resolver->setRequired('field_registry');
        $resolver->setAllowedTypes('field_registry', FilterFieldRegistry::class);
    }
}
