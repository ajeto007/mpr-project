<?php

namespace App\Presenters;

use Nette;
use App\Model;


class CreateriskPresenter extends BasePresenter
{

    public function renderDefault()
    {
        $this->template->anyVariable = 'any value';
    }

}
