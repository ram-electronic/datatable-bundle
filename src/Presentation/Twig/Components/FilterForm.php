<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Twig\Components;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterConditionFormData;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use RamElectronic\DataTableBundle\Presentation\Form\FilterType;
use RamElectronic\DataTableBundle\Presentation\View\Table;
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
#[AsLiveComponent('FilterForm', template: '@DataTable/components/FilterForm.html.twig')]
class FilterForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    /**
     * (De)hydrated by hand: the type of FilterFormData::$filters is only
     * known from a phpdoc, which Live Components can read only when the app
     * happens to have phpstan/phpdoc-parser and phpdocumentor/type-resolver.
     */
    #[LiveProp(hydrateWith: 'hydrateFormData', dehydrateWith: 'dehydrateFormData')]
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
     * Fills initialFormData, fieldRegistry and actionUrl from a Table on the
     * first render, so a page only passes `:table="table"`.
     *
     * @param Table<mixed>|null $table
     */
    public function mount(?Table $table = null): void
    {
        if ($table instanceof Table) {
            $this->initialFormData = $table->formData;
            $this->fieldRegistry = $table->fieldRegistry;
            $this->actionUrl = $this->generateUrl($table->route, $table->routeParams);
        }
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
    /**
     * @return array{sort: ?string, direction: ?string, page: ?int, search: ?string, filters: list<array{field: ?string, operator: ?string, value: ?string}>}
     */
    public function dehydrateFormData(FilterFormData $data): array
    {
        $filters = [];
        foreach ($data->filters as $condition) {
            if ($condition instanceof FilterConditionFormData) {
                $filters[] = [
                    'field' => $condition->field,
                    'operator' => $condition->operator?->value,
                    'value' => $condition->value,
                ];
            }
        }

        return [
            'sort' => $data->sort,
            'direction' => $data->direction?->value,
            'page' => $data->page,
            'search' => $data->search,
            'filters' => $filters,
        ];
    }

    /**
     * @param array<string, mixed> $data what dehydrateFormData() returned
     */
    public function hydrateFormData(array $data): FilterFormData
    {
        $filters = [];
        foreach (\is_array($data['filters'] ?? null) ? $data['filters'] : [] as $condition) {
            if (! \is_array($condition)) {
                continue;
            }

            $filters[] = new FilterConditionFormData(
                field: self::stringOrNull($condition['field'] ?? null),
                operator: FilterOperator::tryFrom((string) self::stringOrNull($condition['operator'] ?? null)),
                value: self::stringOrNull($condition['value'] ?? null),
            );
        }

        return new FilterFormData(
            sort: self::stringOrNull($data['sort'] ?? null),
            direction: SortDirection::tryFrom((string) self::stringOrNull($data['direction'] ?? null)),
            page: \is_int($data['page'] ?? null) ? $data['page'] : null,
            filters: $filters,
            search: self::stringOrNull($data['search'] ?? null),
        );
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return \is_string($value) ? $value : null;
    }

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
