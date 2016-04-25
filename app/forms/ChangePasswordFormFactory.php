<?php

namespace App\AdminModule\Forms;

use App\Model\Entity\User;
use App\Model\Repository\EmployeeRepository;
use App\Model\Repository\UserRepository;
use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class ChangePasswordFormFactory extends Nette\Object
{
    /** @var EmployeeRepository */
    private $employeeRepository;
    
    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }
    
    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form;

        $form->addPassword('password', 'Stávající heslo:')
            ->setRequired('Vložte prosím stávající heslo.');

        $form->addPassword('newPassword', 'Nové heslo:')
            ->setRequired('Vlože prosím nové heslo.');

        $form->addPassword('newPasswordAgaing', 'Znovu heslo:')
            ->setRequired('Vlože prosím opět nové heslo pro kontrolu.')
            ->addRule(Form::EQUAL, _('V polích pro nové heslo nejsou uvedené stejné hodnoty.'), $form['newPassword']);

        $form->addSubmit('submit', 'Uložit');

        $form->addHidden('id');

        $form->onSuccess[] = array($this, 'formSucceeded');
        return $form;
    }
    
    /**
     * @param Form $form
     * @param $values
     */
    public function formSucceeded(Form $form, $values)
    {
        $user = $this->employeeRepository->getById($values['id']);
        if (!Nette\Security\Passwords::verify($values['password'], $user->getPassword())) {
            $form->addError('incorrectPassword');
            return;
        }

        $data['password'] = Nette\Security\Passwords::hash($values['newPassword']);

        try {
            $this->employeeRepository->updateWhere($data, array('id' => $user->getId()));
        } catch (\Exception $e) {
            $form->addError($e->getMessage());
            Debugger::log($e);
        }
    }
}
