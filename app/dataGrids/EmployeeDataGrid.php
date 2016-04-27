<?php

namespace App\DataGrids;

use App\Model\Repository\EmployeeRepository;
use Nette\Object;
use Ublaboo\DataGrid\DataGrid;

class EmployeeDataGrid extends Object
{
    /** @var EmployeeRepository */
    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function create()
    {
        $grid = new DataGrid();

        $grid->setRememberState(FALSE);

        $grid->setDataSource($this->employeeRepository->getQB());

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

        $grid->addFilterText('name', 'Jméno');

        $grid->addColumnText('position', 'Pozice')
            ->setSortable();

        $grid->addFilterText('position', 'Pozice');

        $grid->addColumnText('email', 'E-mail')
            ->setSortable();

        $grid->addFilterText('email', 'E-mail');

        $grid->addColumnText('phone', 'Telefon')
            ->setSortable();

        $grid->addFilterText('phone', 'Telefon');

        $grid->addAction('edit', 'Upravit', 'Employees:edit')
            ->setIcon('pencil')
            ->setClass('btn btn-xs btn-success');

        $grid->addAction('delete', 'Smazat', 'Employees:delete')
            ->setIcon('trash')
            ->setClass('btn btn-xs btn-danger')
            ->setConfirm('Chcete opravdu odstranit zaměstnance %s?', 'name');

        $grid->setItemsDetail();
        $grid->setTemplateFile(__DIR__ . '/EmployeeDataGrid.latte');

        return $grid;
    }
}