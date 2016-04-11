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
    /** @var \Doctrine\ORM\EntityManager @inject */
    public $entityManager;

    /** @var \App\Model\Repository\EmployeeRepository @inject */
    public $employeeRepository;

    /** @var EmployeeForm @inject */
    public $employeeFormFactory;

    /** @var id */
    protected $id;

    public function renderDefault()
    {
        $this->template->employees = $this->employeeRepository->getAll();
    }

    public function actionEdit($id)
    {
        $this->id = $id;
    }

    public function actionDelete($id)
    {
        $employee = $this->employeeRepository->getById($id);
        $this->entityManager->remove($employee->getAddress());
        $this->entityManager->remove($employee);
        $this->entityManager->flush();
        $this->flashMessage('Uživatel ' . $employee->getName() . ' smazán');
        $this->redirect('default');
    }

    public function formAdd($form)
    {
        $values = $form->getValues();
    
        $address = new Address();
        $address->setStreet($values->street);
        $address->setPostcode($values->postcode);
        $address->setCity($values->city);

        $employee = new Employee();
        $employee->setEmail($values->email);
        $employee->setName($values->name);
        $employee->setAddress($address);
        $employee->setPassword(Passwords::hash($values->password));
        $employee->setBirthday(new \DateTime($values->birthday));
        $employee->setPhone($values->phone); 
        $employee->setRole($values->role);
        $employee->setPosition($values->position);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();
        $this->flashMessage('Uživatel ' . $employee->getName() . ' přidán');
        $this->redirect('default');
    }

    public function formEdit($form)
    {
        $values = $form->getValues();

        $employee = $this->employeeRepository->getById($values->id);
        $employee->setEmail($values->email);
        $employee->setName($values->name);
        $employee->setBirthday(new \DateTime($values->birthday));
        $employee->setPhone($values->phone); 
        $employee->setPosition($values->position);
        $employee->setRole($values->role);

        $address = $employee->getAddress();
        $address->setStreet($values->street);
        $address->setPostcode($values->postcode);
        $address->setCity($values->city);
        
        $this->entityManager->merge($address);
        $this->entityManager->merge($employee);
        $this->entityManager->flush();
        $this->flashMessage('Uživatel ' . $employee->getName() . ' editován');
        $this->redirect('default');

    }

    protected function createComponentEmployeeForm()
    {
        $control = $this->employeeFormFactory->create();
        $control->onSuccess[] = array($this, 'formAdd');

        return $control;
    }

    protected function createComponentEmployeeFormEdit()
    {
        $control = $this->employeeFormFactory->create();
        $control['submit']->caption = 'Aktualizovat';
        unset($control['password']);
        $control->onSuccess[] = array($this, 'formEdit');
        $control->addHidden('id', $this->id);
        $employee = $this->employeeRepository->getById($this->id);
        $address = $employee->getAddress();

        $control->setDefaults(array(
            'email' => $employee->getEmail(),
            'name' => $employee->getName(),
            'birthday' => $employee->getBirthday()->format("Y-m-d"),
            'phone' => $employee->getPhone(),
            'position' => $employee->getPosition(),
            'role' => $employee->getRole(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'postcode' => $address->getPostcode(),
        ));

        return $control;
    }
}
