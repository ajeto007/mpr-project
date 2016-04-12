<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Forms\RiskForm;
use App\Model\Repository\CategoryRepository;;
use App\Model\Repository\RiskRepository;;

class RisksPresenter extends BasePresenter
{
    /** @var CategoryRepository @inject */
    public $categoryRepository;
    /** @var RiskRepository @inject */
    public $riskRepository;
    /** @var RiskForm @inject */
    public $riskFormFactory;

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
}
