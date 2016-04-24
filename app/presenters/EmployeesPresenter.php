<?php

namespace App\Presenters;

use App\AdminModule\Forms\ChangePasswordFormFactory;
use App\DataGrids\EmployeeDataGrid;
use Nette;
use App\Model;
use App\Forms\EmployeeForm;
use App\Model\Repository\EmployeeRepository;

class EmployeesPresenter extends BasePresenter
{
    /** @var EmployeeRepository @inject */
    public $employeeRepository;
    /** @var EmployeeForm @inject */
    public $employeeFormFactory;
    /** @var EmployeeDataGrid @inject */
    public $employeeDataGrid;
    /** @var ChangePasswordFormFactory @inject */
    public $changePasswordFormFactory;

    public function renderDefault()
    {
        $this->template->employees = $this->employeeRepository->getAll();
    }

    public function actionProfile()
    {
        $employee = $this->employeeRepository->getById($this->user->id);
        $address = $employee->getAddress();
        $data = $employee->getAsArray();
        $data['birthday'] = $data['birthday']->format('Y-m-d');
        $data = array_merge($data, $address->getAsArray());
        $this['employeeForm']->setDefaults($data);
    }

    public function actionChangePassword()
    {
        $this['changePasswordForm']['id']->setValue($this->user->id);
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
        $this->employeeRepository->delete($employee);
        $this->flashMessage('Uživatel ' . $employee->getName() . ' smazán');
        $this->redirect('default');
    }

    public function formSuccess($form)
    {
        if ($this->getAction() == 'profile') {
            $this->flashMessage('Profil úspěšně aktualizován', 'success');
            $this->redirect('this');
        } else {
            $values = $form->getValues();

            if($values->id) {
                $text = 'aktualizován';
            }
            else {
                $text = 'přidán';
            }

            $this->flashMessage('Uživatel ' . $values->name . ' úspěšně ' . $text, 'success');
            $this->redirect('default');
        }
    }

    public function formError($form)
    {
        $this->flashMessage('Něco se nepovedlo', 'danger');
    }

    protected function createComponentEmployeeForm()
    {
        $control = $this->employeeFormFactory->create();
        $control->onSuccess[] = array($this, 'formSuccess');
        $control->onError[] = array($this, 'formError');
        return $control;
    }

    protected function createComponentEmployeeDataGrid()
    {
        $control = $this->employeeDataGrid->create();
        return $control;
    }
    
    protected function createComponentChangePasswordForm()
    {
        $form = $this->changePasswordFormFactory->create();
        $form->onSuccess[] = function ($form) {
            $this->flashMessage('Heslo bylo úspěšně změněno.', 'success');
            $this->redirect('this');
        };
        $form->onError[] = function ($form) {
            $errors = $form->getErrors();
            if ($errors[0] == 'incorrectPassword') {
                $this->flashMessage('Špatně zadané současné heslo. Zkuste to prosím znovu.', 'warning');
            } else {
                $this->flashMessage('Heslo se nepodařilo změnit. Zkuste to prosím znovu.', 'danger');
            }
        };
        return $form;
    }
}
