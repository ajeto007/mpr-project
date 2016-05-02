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
    /** @var Nette\Mail\SmtpMailer */
    public $mailer;
    /** @var Nette\Application\LinkGenerator */
    public $linkGenerator;

    public function __construct(
        EmployeeRepository $employeeRepository,
        Nette\Mail\SmtpMailer $mailer,
        Nette\Application\LinkGenerator $linkGenerator
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->mailer = $mailer;
        $this->linkGenerator = $linkGenerator;
    }

    public function create()
    {
        $form = new Form;

        $form->addText('email', 'Email')
            ->addRule(Form::EMAIL, 'Zadej validní email')
            ->setAttribute('class', 'form-control');

        $form->addText('name', 'Jméno')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addText('phone', 'Telefon')
            ->addRule(Form::PATTERN, 'Zadej validni telefon', '[0-9]{3} [0-9]{3} [0-9]{3}')
            ->setAttribute('class', 'form-control')
            ->setOption('description', '789 456 123');

        $form->addText('birthday', 'Datum narození')
            ->setAttribute('class', 'form-control')
            ->setOption('description', 'mm/dd/rrrr');

        $form->addText('street', 'Ulice, Č.P')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);

        $form->addText('city', 'Město')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);

        $form->addText('postcode', 'PSČ')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::PATTERN, 'Zadej PSČ ve formátu NNNNN', '[0-9]{5}');

        $form->addText('position', 'Pozice')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addSelect('role', 'Role', Employee::$roles)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addSubmit('submit', 'Odeslat')
            ->setAttribute('class', 'btn btn-default');

        $form->addHidden('id');

        $form->onSuccess[] = array($this, 'processForm');
        return $form;
    }

    public function processForm(Form $form, $values)
    {
        $sendMail = false;

        if ($values->id) {
            $employee = $this->employeeRepository->getById($values->id);
            $address = $employee->getAddress();

            if ($employee->getRole() == 'bezprihlasovani' && $values->role != 'bezprihlasovani') {
                $sendMail = true;
            }
        } else {
            $address = new Address();
            $employee = new Employee();
            $sendMail = true;
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

        if ($sendMail) {
            $password = Nette\Utils\Random::generate();
            $mail = new Nette\Mail\Message();
            $mail->setFrom('NoReply MPR <noreply@mpr.cz>')
                ->addTo($values->email)
                ->setSubject('Registrace do administrace')
                ->setBody("Dobrý den,\nPřihlašovací údaje do administrace na stránce " .
                    $this->linkGenerator->link('Homepage:') . " jsou:\n" .
                    "Přihlašovací e-mail: " . $values->email . "\n" .
                    "Vygenerované heslo: " . $password);
            $employee->setPassword(Nette\Security\Passwords::hash($password));
        }

        try {
            if ($values->id) {
                $this->employeeRepository->update($employee);
                $this->employeeRepository->update($address);
            } else {
                $this->employeeRepository->insert($employee);
                $this->employeeRepository->insert($address);
            }

            if ($sendMail) {
                $this->mailer->send($mail);
            }
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            $form->addError($e);
        } catch (\Exception $e) {
            Debugger::log($e);
            $form->addError($e->getMessage());
        }
    }
}
