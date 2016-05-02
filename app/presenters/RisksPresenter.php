<?php

namespace App\Presenters;

use App\Model\Entity\Risk;
use Nette;
use App\Model;
use App\Forms\RiskForm;
use App\Model\Repository\CategoryRepository;
use App\Model\Repository\RiskRepository;;
use App\DataGrids\RiskDataGrid;
use App\DataGrids\CategoryDataGrid;

class RisksPresenter extends BasePresenter
{
    /** @var CategoryRepository @inject */
    public $categoryRepository;
    /** @var RiskRepository @inject */
    public $riskRepository;
    /** @var RiskForm @inject */
    public $riskFormFactory;
    /** @var RiskDataGrid @inject */
    public $riskDataGrid;
    /** @var RiskDataGrid @inject */
    public $matrixRiskDataGrid;
    /** @var CategoryDataGrid @inject */
    public $categoryDataGrid;
    /** @var integer */
    private $projectId;
    /** @var string */
    private $impacts;
    /** @var string */
    private $probability;

    public function renderCategories()
    {
        $this->template->categories = $this->categoryRepository->getAll();
    }

    public function renderDefault()
    {
        $this->template->risks = $this->riskRepository->getAll();
    }

    public function actionEdit($id)
    {
        $risk = $this->riskRepository->getById($id);
        $data = $risk->getAsArray();
        $this['riskForm']->setDefaults($data);
    }

    public function actionDelete($id)
    {
        $risk = $this->riskRepository->getById($id);

        if (is_null($risk)) {
            throw new Nette\Application\BadRequestException();
        } elseif ($this->user->isInRole('vedouci') && $this->user->id != $risk->getProject()->getLeader()->getId()) {
            throw new Nette\Application\ForbiddenRequestException();
        }

        try {
            $this->riskRepository->delete($risk);
            $this->flashMessage('Riziko ' . $risk->getName() . ' smazáno');
        } catch (\Exception $e) {
            $this->flashMessage('Riziko se nepodařilo smazat.', 'danger');
        }
        $this->redirect('default');
    }

    public function actionActivate($id)
    {
        $risk = $this->riskRepository->getById($id);

        if (is_null($risk)) {
            throw new Nette\Application\BadRequestException();
        } elseif ($this->user->isInRole('vedouci') && $this->user->id != $risk->getProject()->getLeader()->getId()) {
            throw new Nette\Application\ForbiddenRequestException();
        }

        $risk = $this->riskRepository->getById($id);
        $risk->setState('aktivni');
        $risk->setActivated(new \DateTime());
        $this->riskRepository->update($risk);
        $this->redirect('default');
    }

    public function actionDeactivate($id)
    {
        $risk = $this->riskRepository->getById($id);

        if (is_null($risk)) {
            throw new Nette\Application\BadRequestException();
        } elseif ($this->user->isInRole('vedouci') && $this->user->id != $risk->getProject()->getLeader()->getId()) {
            throw new Nette\Application\ForbiddenRequestException();
        }
        
        $risk = $this->riskRepository->getById($id);
        $risk->setState('neaktivni');
        $this->riskRepository->update($risk);
        $this->redirect('default');
    }

    public function formSuccess($form)
    {
        $values = $form->getValues();

        if($values->id) {
            $text = 'aktualizováno';
        }
        else {
            $text = 'přidáno';
        }

        $this->flashMessage('Riziko ' . $values->name . ' úspěšně ' . $text, 'success');
        $this->redirect('default');
    }

    public function formError($form)
    {

        $this->flashMessage('Něco se nepovedlo', 'danger');
    }

    protected function createComponentRiskForm()
    {
        $control = $this->riskFormFactory->create();
        $control->onSuccess[] = array($this, 'formSuccess');
        $control->onError[] = array($this, 'formError');
        return $control;
    }

    protected function createComponentRiskDataGrid()
    {
        $control = $this->riskDataGrid->create();
        return $control;
    }

    protected function createComponentCategoryDataGrid()
    {
        $control = $this->categoryDataGrid->create();
        return $control;
    }

    protected function createComponentMatrixDataGrid()
    {
        $this->matrixRiskDataGrid->useMatrixView($this->projectId, $this->impacts, $this->probability);
        $control = $this->matrixRiskDataGrid->create();
        return $control;
    }

    public function renderMatrix()
    {
        $source = $this->riskRepository->getQB()
            ->join('table.project', 'pr')
            ->join('pr.leader', 'le')
            ->join('pr.employees', 'em');

        if ($this->user->isInRole('zamestnanec') || $this->user->isInRole('vedouci')) {
            $source->where('em.id = :user')
                ->setParameter('user', $this->user->id);
            if ($this->user->isInRole('vedouci')) {
                $source->orWhere('le.id = :leader')
                    ->setParameter('leader', $this->user->id);
            }
        }

        $projects = array();
        /** @var Risk[] $risks */
        $risks = $source->getQuery()->getResult();

        foreach ($risks as $risk) {
            $projects[$risk->getProject()->getId()] = array(
                'name' => $risk->getProject()->getName(),
                'matrix' => $this->getEmptyMatrix()
            );
        }

        foreach ($risks as $risk) {
            $projectId = $risk->getProject()->getId();
            $projects[$projectId]['matrix'][$risk->impacts][$risk->probability]['count']++;
        }

        $this->template->probabilities = Risk::$probabilityEnum;
        $this->template->impacts = Risk::$impactsEnum;
        $this->template->projects = $projects;
    }

    private function getEmptyMatrix()
    {
        $matrix = array();
        foreach (Risk::$impactsEnum as $iKey => $iValue) {
            $matrix[$iKey] = [];
            foreach (Risk::$probabilityEnum as $pKey => $pValue) {
                $matrix[$iKey][$pKey]['count'] = 0;;
            }
        }
        return $matrix;
    }

    public function handleShowMatrixDataGrid($projectId, $impacts, $probability)
    {
        $this->projectId = $projectId;
        $this->impacts = $impacts;
        $this->probability = $probability;
        $this->redrawControl('matrixDataGrid');
    }
}
