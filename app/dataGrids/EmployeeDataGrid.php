<?php

namespace App\DataGrids;

use App\Model\Entity\Employee;
use App\Model\Repository\EmployeeRepository;
use Nette\Object;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;

class EmployeeDataGrid extends Object
{
    /** @var EmployeeRepository */
    private $employeeRepository;
    /** @var User */
    private $user;

    public function __construct(EmployeeRepository $employeeRepository, User $user)
    {
        $this->employeeRepository = $employeeRepository;
        $this->user = $user;
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

        $grid->addColumnText('role', 'Role')
            ->setReplacement(Employee::$roles)
            ->setSortable();

        $grid->addFilterText('role', 'Pozice');

        $grid->addColumnText('email', 'E-mail')
            ->setSortable();

        $grid->addFilterText('email', 'E-mail');

        $grid->addColumnText('phone', 'Telefon')
            ->setSortable();

        $grid->addFilterText('phone', 'Telefon');

        if ($this->user->isAllowed('Employees', 'edit')) {
            $grid->addAction('edit', 'Upravit', 'Employees:edit')
                ->setIcon('pencil')
                ->setClass('btn btn-xs btn-success');

            $grid->addAction('delete', 'Smazat', 'Employees:delete')
                ->setIcon('trash')
                ->setClass('btn btn-xs btn-danger')
                ->setConfirm('Chcete opravdu odstranit zaměstnance %s?', 'name');
        }

        $grid->setItemsDetail();
        $grid->setTemplateFile(__DIR__ . '/EmployeeDataGrid.latte');

        return $grid;
    }
}