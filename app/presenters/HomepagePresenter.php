<?php

namespace App\Presenters;

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

    public $matrix = [];

    public function renderDefault()
    {
        $user = $this->getUser();
        $actualUser = $user->getIdentity();
        $this->template->myProjects = $this->projectRepository->getByUser($actualUser);
        $this->template->newRisks = $this->riskRepository->getNewestRisk(5);
        $this->template->activatedRisks = $this->riskRepository->getActivatedRisk(5);

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
}
