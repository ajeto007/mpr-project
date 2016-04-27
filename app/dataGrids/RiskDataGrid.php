<?php

namespace App\DataGrids;

use App\Model\Entity\Risk;
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

        $grid->setRememberState(FALSE);

        $source = $this->riskRepository->getQB()
            ->join('table.category', 'ca')
            ->join('table.project', 'pr');

        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

        $grid->addFilterText('name', 'Jméno');

        $grid->addColumnText('category_name', 'Kategorie', 'category.name')
            ->setSortable('ca.name');

        $grid->addFilterText('category_name', 'Kategorie', 'ca.name');

        $grid->addColumnText('project_name', 'Projekt', 'project.name')
            ->setSortable('pr.name');

        $grid->addFilterText('project_name', 'Projekt', 'pr.name');

        $grid->addColumnText('impacts', 'Dopad')
            ->setReplacement(Risk::$impactsEnum)
            ->setSortable();

        $grid->addFilterSelect('impacts', 'Dopad', array_merge(array('' => '-'), Risk::$impactsEnum));

        $grid->addColumnText('probability', 'Pravděpodobnost')
            ->setReplacement(Risk::$probabilityEnum)
            ->setSortable();

        $grid->addFilterSelect('probability', 'Pravděbodobnost', array_merge(array('' => '-'), Risk::$probabilityEnum));

        $grid->addColumnText('state', 'Stav')
            ->setReplacement(Risk::$stateEnum)
            ->setSortable();

        $grid->addFilterSelect('state', 'Stav', array_merge(array('' => '-'), Risk::$stateEnum));

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
