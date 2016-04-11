<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use App\Model\Entity\Employee;
use Tracy\Debugger;

class EmployeeForm extends Nette\Object
{
    private $entityManager;
    
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $form->addPassword('password', 'Heslo')
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
        $form->addSubmit('submit', 'Vložit')
            ->setAttribute('class', 'btn btn-default');

        return $form;
    }

}
