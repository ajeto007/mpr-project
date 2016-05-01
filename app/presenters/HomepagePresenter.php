<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Model\Entity\Risk;
use App\Model\Repository\RiskRepository;

class HomepagePresenter extends BasePresenter
{
    /** @var RiskRepository @inject */
    public $riskRepository;

    public $matrix = [];

    public function renderDefault()
    {
        $risks = $this->riskRepository->getAll();

        foreach(Risk::$impactsEnum as $iKey => $iValue) {
            $this->matrix[$iKey] = [];
            foreach(Risk::$probabilityEnum as $pKey => $pValue) {
                $this->matrix[$iKey][$pKey] = [];
            }
        }

        foreach($risks as $risk) {
            array_push($this->matrix[$risk->impacts][$risk->probability], $risk);
        }

        $this->template->probabilities = Risk::$probabilityEnum;
        $this->template->impacts = Risk::$impactsEnum;
        $this->template->matrix = $this->matrix;
    }



}
