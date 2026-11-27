<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Integration;

use RamElectronic\DataTableBundle\Application\ReadModel\Column;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterFieldRegistry;
use RamElectronic\DataTableBundle\Application\ReadModel\FilterOperator;
use RamElectronic\DataTableBundle\Application\ReadModel\PaginatedResult;
use RamElectronic\DataTableBundle\Application\ReadModel\Sort;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Application\ReadModel\TableCriteria;
use RamElectronic\DataTableBundle\Presentation\Service\FilterCollectionBuilder;
use RamElectronic\DataTableBundle\Presentation\Service\TableCriteriaBuilder;
use RamElectronic\DataTableBundle\Presentation\Service\TableHandler;
use RamElectronic\DataTableBundle\Presentation\View\Table;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

/**
 * TableHandler turns a list request into a Table, and FilterForm,
 * SortableHeader and PaginationControl render from that Table alone.
 */
final class TableHandlerTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    private const int PAGE_SIZE = 10;

    /** @var list<TableCriteria> criteria the finder was called with */
    private array $finderCalls = [];

    public function testHandleBuildsTheTableFromTheQueryString(): void
    {
        $table = $this->handle([
            'search' => 'ada',
            'sort' => 'email',
            'direction' => 'DESC',
            'page' => '2',
            'filters' => [['field' => 'name', 'operator' => 'contains', 'value' => 'a']],
        ]);

        $this->assertCount(1, $this->finderCalls);
        $criteria = $this->finderCalls[0];
        $this->assertSame($criteria, $table->criteria);
        $this->assertSame('ada', $criteria->search);
        $this->assertEquals(new Sort('email', SortDirection::DESC), $table->getSort());
        $this->assertSame(2, $criteria->getPage());
        $this->assertSame(FilterOperator::CONTAINS, $criteria->filters->getConditions()[0]->operator);

        $this->assertSame(['row 1', 'row 2'], $table->getItems());
        $this->assertFalse($table->isEmpty());
        $this->assertSame('test_list', $table->route);
        $this->assertSame('filter', $table->getFormName());
    }

    public function testWithoutQueryStringTheDefaultSortApplies(): void
    {
        $table = $this->handle([]);

        $this->assertEquals(new Sort('name', SortDirection::ASC), $table->getSort());
        $this->assertSame(1, $table->criteria->getPage());
        $this->assertNull($table->criteria->search);
    }

    public function testInvalidQueryStringFallsBackToTheDefaults(): void
    {
        $table = $this->handle(['page' => '-1', 'search' => 'ada']);

        $this->assertSame(1, $table->criteria->getPage());
        $this->assertNull($table->criteria->search);
        $this->assertNull($table->formData->search);
    }

    public function testRequestWithoutRouteIsRejected(): void
    {
        $this->expectException(\LogicException::class);

        $this->handler()->handle(Request::create('/list'), $this->registry(), new Sort('name', SortDirection::ASC), $this->finder(...));
    }

    public function testComponentsRenderFromTheTableAlone(): void
    {
        $table = $this->handle(['search' => 'ada', 'sort' => 'name', 'direction' => 'DESC']);

        $filterForm = $this->renderTwigComponent('FilterForm', ['table' => $table])->toString();
        $this->assertStringContainsString('action="/list"', $filterForm);
        $this->assertStringContainsString('value="ada"', $filterForm);

        $header = $this->renderTwigComponent('DataTable:Table:SortableHeader', [
            'table' => $table,
            'sortKey' => 'name',
            'label' => 'label.name',
        ])->toString();
        $this->assertStringContainsString('filter%5Bsearch%5D=ada', $header, 'keeps the search');
        $this->assertStringContainsString('filter%5Bdirection%5D=ASC', $header, 'toggles the active sort');

        $pagination = $this->renderTwigComponent('PaginationControl', ['table' => $table])->toString();
        $this->assertStringContainsString('Showing 1 to 10 of 25 results', $pagination);
        $this->assertStringContainsString('filter%5Bpage%5D=2', $pagination);
        $this->assertStringContainsString('filter%5Bsearch%5D=ada', $pagination);
    }

    public function testExplicitPropsStillWinOverTheTable(): void
    {
        $table = $this->handle([]);

        $header = $this->renderTwigComponent('DataTable:Table:SortableHeader', [
            'table' => $table,
            'sortKey' => 'name',
            'label' => 'label.name',
            'currentSort' => new Sort('name', SortDirection::ASC),
        ])->toString();

        $this->assertStringContainsString('filter%5Bdirection%5D=DESC', $header);
    }

    /**
     * @param array<string, mixed> $filterQuery
     *
     * @return Table<string>
     */
    private function handle(array $filterQuery): Table
    {
        $request = Request::create('/list', 'GET', [] === $filterQuery ? [] : ['filter' => $filterQuery]);
        $request->attributes->set('_route', 'test_list');
        $request->attributes->set('_route_params', []);
        self::getContainer()->get('request_stack')->push($request);

        return $this->handler()->handle($request, $this->registry(), new Sort('name', SortDirection::ASC), $this->finder(...));
    }

    /**
     * @return PaginatedResult<string>
     */
    private function finder(TableCriteria $criteria): PaginatedResult
    {
        $this->finderCalls[] = $criteria;

        return new PaginatedResult(['row 1', 'row 2'], 25, $criteria->getPage(), self::PAGE_SIZE);
    }

    private function handler(): TableHandler
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get('form.factory');

        return new TableHandler($formFactory, new TableCriteriaBuilder(new FilterCollectionBuilder()));
    }

    private function registry(): FilterFieldRegistry
    {
        return new readonly class([
            new Column('name', DataType::STRING, new TranslatableMessage('label.name')),
            new Column('email', DataType::STRING, new TranslatableMessage('label.email')),
        ]) extends FilterFieldRegistry {};
    }
}
