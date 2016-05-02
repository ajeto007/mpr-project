<?php

namespace App\Presenters;

use App\DataGrids\ProjectDataGrid;
use App\DataGrids\RiskDataGrid;
use Nette;
use App\Model;

use App\Model\Entity\Risk;
use App\Model\Repository\ProjectRepository;
use App\Model\Repository\RiskRepository;

class HomepagePresenter extends BasePresenter
{
    /** @var ProjectRepository @inject */
    public $projectRepository;
    /** @var RiskRepository @inject */
    public $riskRepository;
    /** @var RiskDataGrid @inject */
    public $latestAddedRiskDataGrid;
    /** @var RiskDataGrid @inject */
    public $latestActivatedRiskDataGrid;
    /** @var ProjectDataGrid @inject */
    public $myProjectsDataGrid;
    /** @var array */
    public $matrix = [];

    public function renderDefault()
    {
        $risks = $this->riskRepository->getAll();

        foreach (Risk::$impactsEnum as $iKey => $iValue) {
            $this->matrix[$iKey] = [];
            foreach (Risk::$probabilityEnum as $pKey => $pValue) {
                $this->matrix[$iKey][$pKey] = [];
            }
        }

        foreach ($risks as $risk) {
            array_push($this->matrix[$risk->impacts][$risk->probability], $risk);
        }

        $this->template->probabilities = Risk::$probabilityEnum;
        $this->template->impacts = Risk::$impactsEnum;
        $this->template->matrix = $this->matrix;
    }

    protected function createComponentLatestAddedRiskDataGrid()
    {
        $this->latestAddedRiskDataGrid->setLatestAdded(true);
        $control = $this->latestAddedRiskDataGrid->create();
        return $control;
    }

    protected function createComponentLatestActivatedRiskDataGrid()
    {
        $this->latestActivatedRiskDataGrid->setLatestActivated(true);
        $control = $this->latestActivatedRiskDataGrid->create();
        return $control;
    }

    protected function createComponentMyProjectDataGrid()
    {
        $this->myProjectsDataGrid->setOnDashboard(true);
        $control = $this->myProjectsDataGrid->create();
        return $control;
    }
}
