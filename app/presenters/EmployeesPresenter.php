<?php

namespace App\Presenters;

use Nette;
use App\Model;
use App\Forms\EmployeeForm;
use App\Model\Entity\Address;
use App\Model\Entity\Employee;
use Nette\Security\Passwords;
use App\Model\Repository\EmployeeRepository;

class EmployeesPresenter extends BasePresenter
{
    /** @var \App\Model\Repository\EmployeeRepository @inject */
    public $employeeRepository;

    /** @var EmployeeForm @inject */
    public $employeeFormFactory;


    public function renderDefault()
    {
        $this->template->employees = $this->employeeRepository->getAll();
    }

    public function actionEdit($id)
    {
        $employee = $this->employeeRepository->getById($id);
		$address = $employee->getAddress();
		$data = $employee->getAsArray();
		$data['birthday'] = $data['birthday']->format('Y-m-d');
		$data = array_merge($data, $address->getAsArray());
		$this['employeeForm']->setDefaults($data);
    }

    public function actionDelete($id)
    {
        $employee = $this->employeeRepository->getById($id);
        $employeeRepository->delete($employee);
        $this->flashMessage('Uživatel ' . $employee->getName() . ' smazán');
        $this->redirect('default');
    }

	public function formSuccess($form)
	{
		$values = $form->getValues();

		if($values->id)
		{
			$text = 'aktualizován';
		}
		else
		{
			$text = 'přidán';
		}

		$this->flashMessage('Uživatel ' . $values->name . ' úspěšně ' . $text);
		$this->redirect('default');
	}

	public function formError($form)
	{
		$this->flashMessage('Něco se nepovedlo', 'error');
	}

    protected function createComponentEmployeeForm()
    {
        $control = $this->employeeFormFactory->create();
		// $control->onSuccess[] = array($this, 'formSuccess');
		// $control->onError[] = array($this, 'formError');
        return $control;
    }
}
