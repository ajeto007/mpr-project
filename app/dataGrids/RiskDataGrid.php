<?php

namespace App\DataGrids;

use App\Model\Entity\Risk;
use App\Model\Repository\RiskRepository;
use Nette\Object;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;

class RiskDataGrid extends Object
{
    /** @var RiskRepository */
    private $riskRepository;
    /** @var User */
    private $user;
    /** @var boolean */
    private $latestAdded = false;
    /** @var boolean */
    private $latestActivated = false;
    /** @var boolean */
    private $matrixView = false;
    /** @var integer */
    private $projectId;
    /** @var string */
    private $impacts;
    /** @var string */
    private $probability;

    public function __construct(RiskRepository $riskRepository, User $user)
    {
        $this->riskRepository = $riskRepository;
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getLatestAdded()
    {
        return $this->latestAdded;
    }

    /**
     * @param mixed $latestAdded
     */
    public function setLatestAdded($latestAdded)
    {
        $this->latestAdded = $latestAdded;
    }

    /**
     * @return boolean
     */
    public function isLatestActivated()
    {
        return $this->latestActivated;
    }

    /**
     * @param boolean $latestActivated
     */
    public function setLatestActivated($latestActivated)
    {
        $this->latestActivated = $latestActivated;
    }

    /**
     * @return mixed
     */
    public function getMatrixView()
    {
        return $this->matrixView;
    }

    /**
     * @param $projectId
     * @param $impacts
     * @param $probability
     */
    public function useMatrixView($projectId, $impacts, $probability)
    {
        $this->matrixView = true;
        $this->projectId = $projectId;
        $this->impacts = $impacts;
        $this->probability = $probability;
    }

    public function create()
    {
        $grid = new DataGrid();

        $grid->setRememberState(FALSE);

        $source = $this->riskRepository->getQB()
            ->join('table.category', 'ca')
            ->join('table.project', 'pr')
            ->join('pr.leader', 'le')
            ->leftJoin('pr.employees', 'em');

        if ($this->user->isInRole('zamestnanec') || $this->user->isInRole('vedouci')) {
            $source->where('em.id = :user')
                ->setParameter('user', $this->user->id);
            if ($this->user->isInRole('vedouci')) {
                $source->orWhere('le.id = :leader')
                    ->setParameter('leader', $this->user->id);
            }
        }

        if ($this->latestAdded || $this->latestActivated) {
            $source->orderBy('table.created', 'DESC')
                ->setMaxResults(5);
        }

        if ($this->latestActivated) {
            $source->andWhere('table.state = :state')
                ->setParameter('state', 'aktivni');
        }

        if ($this->matrixView) {
            $source->andWhere('pr.id = :project')
                ->andWhere('table.impacts = :impacts')
                ->andWhere('table.probability = :probability')
                ->setParameter('project', $this->projectId)
                ->setParameter('impacts', $this->impacts)
                ->setParameter('probability', $this->probability);
        }

        $grid->setDataSource($source);

        $grid->addColumnText('name', 'Jméno');

        $grid->addColumnText('category_name', 'Kategorie', 'category.name');

        $grid->addColumnText('project_name', 'Projekt', 'project.name');

        if (!$this->latestAdded && !$this->latestActivated) {
            $grid->getColumn('name')
                ->setSortable();

            $grid->addFilterText('name', 'Jméno');

            $grid->getColumn('category_name')
                ->setSortable('ca.name');

            $grid->addFilterText('category_name', 'Kategorie', 'ca.name');

            $grid->getColumn('project_name')
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

            if ($this->user->isAllowed('Risks', 'edit') && !$this->matrixView) {
                $grid->addAction('edit', 'Upravit', 'Risks:edit')
                    ->setIcon('pencil')
                    ->setClass('btn btn-xs btn-success');

                $grid->addAction('delete', 'Smazat', 'Risks:delete')
                    ->setIcon('trash')
                    ->setClass('btn btn-xs btn-danger')
                    ->setConfirm('Chcete opravdu odstranit riziko %s?', 'name');

                if ($this->user->isInRole('vedouci')) {
                    $grid->allowRowsAction('edit', function($item) {
                        /** @var Risk $item */
                        return $item->getProject()->getLeader()->getId() == $this->user->id;
                    });

                    $grid->allowRowsAction('delete', function($item) {
                        /** @var Risk $item */
                        return $item->getProject()->getLeader()->getId() == $this->user->id;
                    });
                }

                $grid->addAction('activate', 'Aktivovat', 'Risks:activate')
                    ->setIcon('pencil')
                    ->setClass('btn btn-xs btn-success');

                $grid->allowRowsAction('activate', function($item) {
                    $rights = true;
                    if ($this->user->isInRole('vedouci')) {
                        $rights = $item->getProject()->getLeader()->getId() == $this->user->id;
                    }
                    return $item->state == 'neaktivni' && $rights;
                });

                $grid->addAction('deactivate', 'Deaktivovat', 'Risks:deactivate')
                    ->setIcon('pencil')
                    ->setClass('btn btn-xs btn-danger');

                $grid->allowRowsAction('deactivate', function($item) {
                    $rights = true;
                    if ($this->user->isInRole('vedouci')) {
                        $rights = $item->getProject()->getLeader()->getId() == $this->user->id;
                    }
                    return $item->state == 'aktivni' && $rights;
                });
            }
        }

        if (!$this->matrixView) {
            $grid->setItemsDetail();
            $grid->setTemplateFile(__DIR__ . '/RiskDataGrid.latte');
        }

        return $grid;
    }
}
