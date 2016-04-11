<?php

namespace App\Forms;

use App\Model\Entity\Address;
use App\Model\Repository\EmployeeRepository;
use Nette;
use Nette\Application\UI\Form;
use App\Model\Entity\Employee;
use Nette\Security\Passwords;
use Nette\Utils\Random;
use Tracy\Debugger;

class EmployeeForm extends Nette\Object
{
    /** @var EmployeeRepository */
    public $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function create()
    {
        $form = new Form;

        $form->addText('email', 'Email')
            ->addRule(Form::PATTERN, 'Zadej validní email', '[a-zA-Z0-9\.\-_]+@[0-9a-zA-Z\.\-]+\.[a-z]+')
            ->setAttribute('class', 'form-control');
        $form->addText('name', 'Jméno')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        $form->addText('phone', 'Telefon')
            ->addRule(Form::PATTERN, 'Zadej validni telefon', '[0-9]{9}')
            ->setAttribute('class', 'form-control')
            ->setOption('description', '123456789');
        $form->addText('birthday', 'Datum narození')
            ->addRule(Form::PATTERN, 'Zadej validní datum', '[0-9]{4}-[0-9]{2}-[0-9]{2}')
            ->setAttribute('class', 'form-control')
            ->setOption('description', 'yyyy-mm-dd');
        $form->addText('street', 'Ulice, Č.P')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);
        $form->addText('city', 'Město')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);
        $form->addText('postcode', 'PSČ')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);

        $form->addSelect('position', 'Pozice', Employee::$positions)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addSelect('role', 'Role', Employee::$roles)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        //
        $form->addSubmit('submit', 'Odeslat')
            ->setAttribute('class', 'btn btn-default');

        $form->addHidden('id');

        $form->onSuccess[] = array($this, 'processForm');
        return $form;
    }

    public function processForm(Form $form, $values)
    {
        if($values->id) {
            $employee = $this->employeeRepository->getById($values->id);
            $address = $employee->getAddress();
        } else {
            $address = new Address();
            $employee = new Employee();
            $employee->setPassword(Passwords::hash(Random::generate()));
        }
    
        $address->setStreet($values->street);
        $address->setPostcode($values->postcode);
        $address->setCity($values->city);

        $employee->setEmail($values->email);
        $employee->setName($values->name);
        $employee->setAddress($address);
        $employee->setBirthday(new \DateTime($values->birthday));
        $employee->setPhone($values->phone); 
        $employee->setRole($values->role);
        $employee->setPosition($values->position);

        try {
            if ($values->id) {
                $this->employeeRepository->update($employee);
                $this->employeeRepository->update($address);
            } else {
                $this->employeeRepository->insert($employee);
                $this->employeeRepository->insert($address);
            }
        } catch (\Exception $e) {
            Debugger::log($e);
            $form->addError($e->getMessage());
        }
    }
}
