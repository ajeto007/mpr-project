<?php

namespace App\DataGrids;

use App\Model\Repository\ProjectRepository;
use Nette\Object;
use Ublaboo\DataGrid\DataGrid;

class ProjectDataGrid extends Object
{
    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function create()
    {
        $grid = new DataGrid();

        $grid->setRememberState(FALSE);

        $source = $this->projectRepository->getQB()
            ->join('table.leader', 'em');

        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

        $grid->addFilterText('name', 'Jméno');

        $grid->addColumnText('leader', 'Vedoucí', 'leader.name')
            ->setSortable('em.name');

        $grid->addFilterText('leader', 'Vedoucí', 'em.name');

        $grid->addColumnDateTime('fromDate', 'Datum začátku')
            ->setSortable();

        $grid->addFilterDateRange('fromDate', 'Datum začátku');

        $grid->addColumnDateTime('toDate', 'Datum konce')
            ->setSortable();

        $grid->addFilterDateRange('toDate', 'Datum konce');

        $grid->addAction('edit', 'Upravit', 'Projects:edit')
            ->setIcon('pencil')
            ->setClass('btn btn-xs btn-success');

        $grid->addAction('delete', 'Smazat', 'Projects:delete')
            ->setIcon('trash')
            ->setClass('btn btn-xs btn-danger')
            ->setConfirm('Chcete opravdu odstranit projekt?', 'name');

        $grid->setItemsDetail();
        $grid->setTemplateFile(__DIR__ . '/ProjectDataGrid.latte');

        return $grid;
    }
}