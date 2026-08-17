<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use RamElectronic\DataTableBundle\Presentation\Form\FilterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

/**
 * Generic live component for table filtering.
 * Handles dynamic filter collections with LiveCollectionTrait.
 */
#[AsLiveComponent]
class FilterForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public FilterFormData $initialFormData;

    #[LiveProp]
    public string $actionUrl;

    /**
     * The form type class to use for rendering the filter form.
     * Defaults to FilterType if not specified.
     *
     * @var class-string<FilterType>
     */
    #[LiveProp]
    public string $formTypeClass = FilterType::class;

    #[LiveProp(hydrateWith: 'hydrateFieldRegistry', dehydrateWith: 'dehydrateFieldRegistry')]
    public FilterFieldRegistry $fieldRegistry;

    public function __construct()
    {
        $this->initialFormData = new FilterFormData();
    }

    /**
     * @return FormInterface<FilterFormData>
     */
    #[\Override]
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            $this->formTypeClass,
            data: $this->initialFormData,
            options: ['field_registry' => $this->fieldRegistry],
        );
    }

    /**
     * Custom hydration for field registry to handle serialization.
     *
     * PHP disallows serialize() on anonymous classes outright, so the whole
     * registry can't be serialized directly. Instead, persist the concrete
     * class name alongside its $fields array and reconstruct via the
     * constructor, which preserves state without depending on serialize()
     * support for the subclass.
     */
    public function hydrateFieldRegistry(string $data): FilterFieldRegistry
    {
        $payload = unserialize($data, ['allowed_classes' => true]);

        if (
            ! \is_array($payload)
            || ! isset($payload['class'], $payload['fields'])
            || ! \is_string($payload['class'])
            || ! \is_array($payload['fields'])
            || ! is_a($payload['class'], FilterFieldRegistry::class, true)
        ) {
            throw new \UnexpectedValueException('Failed to unserialize a FilterFieldRegistry instance.');
        }

        return new $payload['class']($payload['fields']);
    }

    /**
     * Custom dehydration for field registry to handle serialization.
     */
    public function dehydrateFieldRegistry(FilterFieldRegistry $registry): string
    {
        return serialize([
            'class' => $registry::class,
            'fields' => $registry->getFields(),
        ]);
    }
}
