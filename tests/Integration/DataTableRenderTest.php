<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Integration;

use RamElectronic\DataTableBundle\Application\ReadModel\Action;
use RamElectronic\DataTableBundle\Application\ReadModel\DataType;
use RamElectronic\DataTableBundle\Application\ReadModel\PaginatedResult;
use RamElectronic\DataTableBundle\Application\ReadModel\Sort;
use RamElectronic\DataTableBundle\Application\ReadModel\SortDirection;
use RamElectronic\DataTableBundle\Application\ReadModel\TableColumn;
use RamElectronic\DataTableBundle\Application\ReadModel\TableCriteria;
use RamElectronic\DataTableBundle\Application\ReadModel\TableDefinition;
use RamElectronic\DataTableBundle\Presentation\Service\FilterCollectionBuilder;
use RamElectronic\DataTableBundle\Presentation\Service\TableCriteriaBuilder;
use RamElectronic\DataTableBundle\Presentation\Service\TableHandler;
use RamElectronic\DataTableBundle\Presentation\View\Table;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * <twig:DataTable:List :table="table" /> renders a whole list from a
 * TableDefinition, with the page's cell_<key>, actions and empty blocks.
 */
final class DataTableRenderTest extends KernelTestCase
{
    public function testRendersColumnsSortLinksRowsAndPagination(): void
    {
        $html = $this->render('data_table_plain.html.twig', [
            new DataTableTestRow('Ada', 'active', DataTableTestRole::Admin),
            new DataTableTestRow('<b>Bob</b>', 'inactive', DataTableTestRole::User),
        ], totalCount: 25);

        // Header: sortable column links, plain column text.
        $this->assertStringContainsString('filter%5Bsort%5D=name', $html);
        $this->assertStringNotContainsString('filter%5Bsort%5D=status', $html);
        $this->assertStringContainsString('label.status', $html);

        // Cells through the renderers: escaped text, translated enum.
        $this->assertSame(2, substr_count($html, 'data-datatable-row'));
        $this->assertSame(2 * 4, substr_count($html, '<td'), 'one cell per column plus actions, none nested');
        $this->assertStringContainsString('&lt;b&gt;Bob&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>Bob</b>', $html);
        $this->assertStringContainsString('role:admin', $html);

        // Definition actions, filter form and pagination.
        $this->assertStringContainsString('href="/list?id=', $html, 'edit action from the definition');
        $this->assertStringContainsString('data-live-name-value="FilterForm"', $html);
        $this->assertMatchesRegularExpression('/<div[^>]*class="mb-4"[^>]*data-live-name-value="FilterForm"|<div[^>]*data-live-name-value="FilterForm"[^>]*class="mb-4"/', $html, 'gap between filter box and table');
        $this->assertStringContainsString('Showing 1 to 10 of 25 results', $html);
    }

    public function testPageBlocksReplaceCellsActionsAndEmptyText(): void
    {
        $html = $this->render('data_table_page.html.twig', [
            new DataTableTestRow('Ada', 'active', DataTableTestRole::Admin),
        ]);

        $this->assertStringContainsString('<strong class="status">active!</strong>', $html, 'cell_status sees row');
        $this->assertStringContainsString('<a href="/edit/Ada">edit Ada</a>', $html, 'actions block sees row');
        $this->assertStringNotContainsString('href="/list?id=', $html, 'actions block replaces the definition actions');
        $this->assertStringContainsString('data-test="test-row"', $html);

        $empty = $this->render('data_table_page.html.twig', []);
        $this->assertStringContainsString('Nobody here.', $empty);
    }

    public function testDefaultEmptyText(): void
    {
        $this->assertStringContainsString('No entries found.', $this->render('data_table_plain.html.twig', []));
    }

    /**
     * @param list<DataTableTestRow> $rows
     */
    private function render(string $template, array $rows, int $totalCount = 0): string
    {
        $request = Request::create('/list');
        $request->attributes->set('_route', 'test_list');
        $request->attributes->set('_route_params', []);
        self::getContainer()->get('request_stack')->push($request);

        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get('form.factory');
        $handler = new TableHandler($formFactory, new TableCriteriaBuilder(new FilterCollectionBuilder()));

        $table = $handler->handleDefinition(
            $request,
            new DataTableTestDefinition(),
            static fn (TableCriteria $criteria): PaginatedResult => new PaginatedResult($rows, max($totalCount, \count($rows)), $criteria->getPage(), 10),
        );
        $this->assertInstanceOf(Table::class, $table);

        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');

        return $twig->render($template, ['table' => $table]);
    }
}

final class DataTableTestDefinition extends TableDefinition
{
    public function columns(): array
    {
        return [
            new TableColumn('name', DataType::STRING, new TranslatableMessage('label.name'), sortable: true, filterable: true),
            new TableColumn('status', DataType::STRING, new TranslatableMessage('label.status')),
            new TableColumn('role', DataType::STRING, new TranslatableMessage('label.role'), sortable: true),
        ];
    }

    public function defaultSort(): Sort
    {
        return new Sort('name', SortDirection::ASC);
    }

    public function actions(): array
    {
        return [Action::edit('test_list')];
    }
}

final readonly class DataTableTestRow
{
    public function __construct(
        public string $name,
        public string $status,
        public DataTableTestRole $role,
        public int $id = 7,
    ) {
    }
}

enum DataTableTestRole: string implements TranslatableInterface
{
    case Admin = 'admin';
    case User = 'user';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return 'role:'.$this->value;
    }
}
