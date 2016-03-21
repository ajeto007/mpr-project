<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Tracy\Debugger;

class SignFormFactory extends Nette\Object
{
    /** @var User */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form;
        $form->addText('email', 'E-mail:')
            ->setRequired('Vložte prosím e-mail.')
            ->setAttribute('placeholder', 'E-mail');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Vložte prosím heslo.')
            ->setAttribute('placeholder', 'Heslo');

        $form->addCheckbox('remember', ' Zůstat přihlášen');

        $form->addSubmit('submit', 'Přihlásit se');

        $form->onSuccess[] = array($this, 'formSucceeded');
        return $form;
    }

    public function formSucceeded(Form $form, $values)
    {
        if ($values['remember']) {
            $this->user->setExpiration('14 days', FALSE);
        } else {
            $this->user->setExpiration('20 minutes', TRUE);
        }

        try {
            $this->user->login($values['email'], $values['password']);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
            Debugger::log($e);
        }
    }
}
