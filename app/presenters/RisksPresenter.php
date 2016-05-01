<?php

namespace App\Presenters;

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
    /** @var CategoryDataGrid @inject */
    public $categoryDataGrid;

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
        $this->riskRepository->delete($risk);
        $this->flashMessage('Riziko ' . $risk->getName() . ' smazáno');
        $this->redirect('default');
    }

    public function actionActivate($id)
    {
        $risk = $this->riskRepository->getById($id);
        $risk->setState('aktivni');
        $risk->setActivated(new \DateTime());
        $this->riskRepository->update($risk);
        $this->redirect('default');
    }

    public function actionDeactivate($id)
    {
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
}
