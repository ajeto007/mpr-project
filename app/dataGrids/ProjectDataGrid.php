<?php

namespace App\DataGrids;

use App\Model\Entity\Project;
use App\Model\Repository\ProjectRepository;
use Nette\Object;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;

class ProjectDataGrid extends Object
{
    /** @var ProjectRepository */
    private $projectRepository;
    /** @var boolean */
    private $onDashboard = false;
    /** @var User */
    private $user;

    public function __construct(ProjectRepository $projectRepository, User $user)
    {
        $this->projectRepository = $projectRepository;
        $this->user = $user;
    }

    /**
     * @return boolean
     */
    public function getOnDashboard()
    {
        return $this->onDashboard;
    }

    /**
     * @param boolean $onDashboard
     */
    public function setOnDashboard($onDashboard)
    {
        $this->onDashboard = $onDashboard;
    }

    public function create()
    {
        $grid = new DataGrid();

        $grid->setRememberState(FALSE);

        $source = $this->projectRepository->getQB()
            ->join('table.leader', 'le')
            ->leftJoin('table.employees', 'em')
            ->distinct();

        if ($this->user->isInRole('zamestnanec') || $this->user->isInRole('vedouci')) {
            $source->where('em.id = :user')
                ->setParameter('user', $this->user->id);
            if ($this->user->isInRole('vedouci')) {
                $source->orWhere('le.id = :leader')
                    ->setParameter('leader', $this->user->id);
            }
        }

        if ($this->onDashboard) {
            $grid->setItemsPerPageList(array(5, 10, 20, 50));
            $grid->setDefaultSort(['name' => 'ASC']);
            $source->andWhere('table.toDate > :now')
                ->andWhere('table.fromDate < :now')
                ->setParameter('now', new \DateTime());
        }

        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Jméno');

        $grid->addColumnText('leader', 'Vedoucí', 'leader.name');

        if (!$this->onDashboard) {
            $grid->getColumn('name')
                ->setSortable();

            $grid->addFilterText('name', 'Jméno');

            $grid->getColumn('leader')
                ->setSortable('le.name');

            $grid->addFilterText('leader', 'Vedoucí', 'le.name');

            $grid->addColumnDateTime('fromDate', 'Datum začátku')
                ->setSortable();

            $grid->addFilterDateRange('fromDate', 'Datum začátku');

            $grid->addColumnDateTime('toDate', 'Datum konce')
                ->setSortable();

            $grid->addFilterDateRange('toDate', 'Datum konce');

            if ($this->user->isAllowed('Projects', 'edit')) {
                $grid->addAction('edit', 'Upravit', 'Projects:edit')
                    ->setIcon('pencil')
                    ->setClass('btn btn-xs btn-success');

                $grid->addAction('delete', 'Smazat', 'Projects:delete')
                    ->setIcon('trash')
                    ->setClass('btn btn-xs btn-danger')
                    ->setConfirm('Chcete opravdu odstranit projekt?', 'name');

                if ($this->user->isInRole('vedouci')) {
                    $grid->allowRowsAction('edit', function($item) {
                        /** @var Project $item */
                        return $item->getLeader()->getId() == $this->user->id;
                    });

                    $grid->allowRowsAction('delete', function($item) {
                        /** @var Project $item */
                        return $item->getLeader()->getId() == $this->user->id;
                    });
                }
            }
        }

        $grid->setItemsDetail();
        $grid->setTemplateFile(__DIR__ . '/ProjectDataGrid.latte');

        return $grid;
    }
}