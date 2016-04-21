<?php

namespace App\DataGrids;

use App\Model\Repository\CategoryRepository;
use Nette\Object;
use Ublaboo\DataGrid\DataGrid;

class CategoryDataGrid extends Object
{
    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function create()
    {
        $grid = new DataGrid();

        $grid->setDataSource($this->categoryRepository->getQB());

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

		$grid->addColumnText('parent_name', 'Nadřazená kategorie', 'parent.name')
			->setSortable();

        $grid->setTemplateFile(__DIR__ . '/CategoryDataGrid.latte');

        return $grid;
    }
}
