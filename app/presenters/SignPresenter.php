<?php

namespace App\Presenters;

use App\Forms\ForgottenPasswordFormFactory;
use Nette;
use App\Forms\SignFormFactory;

class SignPresenter extends BasePresenter
{
    /** @var SignFormFactory @inject */
    public $signFormFactory;
    /** @var ForgottenPasswordFormFactory @inject */
    public $forgottenPasswordFormFactory;

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Byl jstě úspěšně odhlášen.', 'success');
        $this->redirect('Sign:in');
    }

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm()
    {
        $form = $this->signFormFactory->create();
        $form->onSuccess[] = function ($form) {
            $this->flashMessage('Byl jstě úspěšně přihlášen.', 'success');
            $this->redirect('Homepage:');
        };
        $form->onError[] = function ($form) {
            if ($form->getErrors()[0] == 'invalid rights') {
                $this->flashMessage('Pro vstup do systému bohužel nemáte požadovaná práva.', 'warning');
            } else {
                $this->flashMessage('Se zadanými údaji se nepodařilo přihlásit. Zkuste to prosím znovu.', 'danger');
            }
        };
        return $form;
    }

    /**
     * Forgotten password form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentForgottenPasswordForm()
    {
        $form = $this->forgottenPasswordFormFactory->create();
        $form->onSuccess[] = function ($form) {
            $this->flashMessage('Nové heslo vám bylo zasláno na email.', 'success');
            $this->redirect('Sign:in');
        };
        $form->onError[] = function ($form) {
            $errors = $form->getErrors();
            if ($errors[0] == 'nonExistUser') {
                $this->flashMessage('Uživatel s tímto e-mailem neexistuje. Zkuste to prosím znovu.', 'warning');
            } else {
                $this->flashMessage('Nepodařilo se vygenerovat a odeslat nové heslo. Zkuste to prosím znovu.', 'danger');
            }
        };
        return $form;
    }
}
