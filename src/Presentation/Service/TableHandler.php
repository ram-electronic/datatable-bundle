<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Presentation\Service;

use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\PaginatedResult;
use RamElectronic\DataTableBundle\Application\ReadModel\Sort;
use RamElectronic\DataTableBundle\Application\ReadModel\TableCriteria;
use RamElectronic\DataTableBundle\Presentation\Dto\FilterFormData;
use RamElectronic\DataTableBundle\Presentation\Form\FilterType;
use RamElectronic\DataTableBundle\Presentation\View\Table;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Turns the request of a list page into a Table: handles the filter form
 * (GET query string), builds the TableCriteria and loads the current page.
 *
 *     $table = $tableHandler->handle($request, $fieldRegistry, new Sort('name', SortDirection::ASC), $repository->findForTable(...));
 *
 * An invalid query string (page=-1, an overlong search) shows the list
 * with the default criteria instead of failing.
 */
final readonly class TableHandler
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private TableCriteriaBuilder $criteriaBuilder,
    ) {
    }

    /**
     * @template T
     *
     * @param callable(TableCriteria): PaginatedResult<T> $finder   loads one page, usually a repository method
     *                                                              built on DoctrineTableReadModelRepository
     * @param class-string<FilterType>                    $formType
     *
     * @return Table<T>
     */
    public function handle(
        Request $request,
        FilterFieldRegistry $fieldRegistry,
        Sort $defaultSort,
        callable $finder,
        string $formType = FilterType::class,
    ): Table {
        $form = $this->formFactory->create($formType, new FilterFormData(), ['field_registry' => $fieldRegistry]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && ! $form->isValid()) {
            $form = $this->formFactory->create($formType, new FilterFormData(), ['field_registry' => $fieldRegistry]);
        }

        $formData = $form->getData();
        if (! $formData instanceof FilterFormData) {
            throw new \LogicException(\sprintf('The form type "%s" must hold a %s.', $formType, FilterFormData::class));
        }

        $criteria = $this->criteriaBuilder->build($formData, $defaultSort);

        $route = $request->attributes->get('_route');
        if (! \is_string($route)) {
            throw new \LogicException('TableHandler needs a request matched by the router (no "_route" attribute).');
        }

        /** @var array<string, mixed> $routeParams */
        $routeParams = $request->attributes->all('_route_params');

        return new Table(
            result: $finder($criteria),
            criteria: $criteria,
            form: $form->createView(),
            formData: $formData,
            fieldRegistry: $fieldRegistry,
            route: $route,
            routeParams: $routeParams,
        );
    }
}
