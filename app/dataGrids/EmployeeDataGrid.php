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

        $grid->setDataSource($this->employeeRepository->getQB());

        $grid->addColumnText('name', 'Jméno')
            ->setSortable();

        $grid->addColumnText('position', 'Pozice')
            ->setSortable();

        $grid->addColumnText('email', 'E-mail')
            ->setSortable();

        $grid->addColumnText('phone', 'Telefon')
            ->setSortable();

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