<?php

namespace App\DataGrids;

use App\Model\Repository\RiskRepository;
use Nette\Object;
use Ublaboo\DataGrid\DataGrid;

class RiskDataGrid extends Object
{
    /** @var RiskRepository */
    private $riskRepository;

    public function __construct(RiskRepository $riskRepository)
    {
        $this->riskRepository = $riskRepository;
    }

    public function create()
    {
        $grid = new DataGrid();

        $grid->setDataSource($this->riskRepository->getQB());

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

		$grid->addColumnText('category_name', 'Kategorie', 'category.name')
			->setSortable();

		$grid->addColumnText('project_name', 'Projekt', 'project.name')
			->setSortable();

        $grid->addColumnText('description', 'Popis')
            ->setSortable();

        $grid->addColumnText('impacts', 'Dopad')
            ->setSortable();

        $grid->addColumnText('probability', 'Pravděpodobnost')
            ->setSortable();

        $grid->addColumnText('state', 'Stav')
            ->setSortable();

        $grid->addAction('edit', 'Upravit', 'Risks:edit')
            ->setIcon('pencil')
            ->setClass('btn btn-xs btn-success');

        $grid->addAction('delete', 'Smazat', 'Risks:delete')
            ->setIcon('trash')
            ->setClass('btn btn-xs btn-danger')
            ->setConfirm('Chcete opravdu odstranit riziko %s?', 'name');

		$grid->addAction('activate', 'Aktivovat', 'Risks:activate')
			->setIcon('pencil')
			->setClass('btn btn-xs btn-success');

		$grid->allowRowsAction('activate', function($item) {
			return $item->state == 'neaktivni';
		});

		$grid->addAction('deactivate', 'Deaktivovat', 'Risks:deactivate')
			->setIcon('pencil')
			->setClass('btn btn-xs btn-danger');

		$grid->allowRowsAction('deactivate', function($item) {
			return $item->state == 'aktivni';
		});

        $grid->setItemsDetail();
        $grid->setTemplateFile(__DIR__ . '/RiskDataGrid.latte');

        return $grid;
    }
}
