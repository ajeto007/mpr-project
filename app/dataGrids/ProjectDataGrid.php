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

        $grid->setDataSource($this->projectRepository->getQB());

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

        $grid->addColumnText('leader', 'Vedoucí')
            ->setRenderer(function($item) {
                $leader = $item->getLeader();
                if ($leader==null) return "neurčen";
                return $item->getLeader()->getName();
            })
            ->setSortable();

        $grid->addColumnDateTime('fromDate', 'Datum začátku')
            ->setSortable();

        $grid->addColumnDateTime('toDate', 'Datum konce')
            ->setSortable();

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