<?php

namespace App\Presenters;

use Nette;
use App\Model;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public function startup()
    {
        parent::startup();
        if (!$this->user->isLoggedIn()) {
            if ($this->getName() == 'Sign' && in_array($this->getAction(), array('in', 'out', 'forgottenPassword'))) {
                return;
            } else {
                $this->redirect('Sign:in');
                return;
            }
        }
    }
}
