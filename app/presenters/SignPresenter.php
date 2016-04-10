<?php

namespace App\Presenters;

use Nette;
use App\Forms\SignFormFactory;

class SignPresenter extends BasePresenter
{
    /** @var SignFormFactory @inject */
    public $signFormFactory;

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
        return $form;
    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Byl jstě úspěšně odhlášen.', 'success');
        $this->redirect('Sign:in');
    }

}
