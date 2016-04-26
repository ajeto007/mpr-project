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
        
        $source = $this->categoryRepository->getQB()
            ->leftJoin('table.parent', 'pa');

        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

        $grid->addFilterText('name', 'Jméno');

        $grid->addColumnText('parent_name', 'Nadřazená kategorie', 'parent.name')
            ->setSortable('pa.name');

        $grid->addFilterText('parent_name', 'Nadřazená kategorie', 'pa.name');

        $grid->setTemplateFile(__DIR__ . '/CategoryDataGrid.latte');

        return $grid;
    }
}
