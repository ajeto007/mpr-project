<?php

namespace App\Presenters;

use Nette;
use App\Model;


class RiskcategoryPresenter extends BasePresenter
{

    public function renderDefault()
    {
        $this->template->anyVariable = 'any value';
    }

}
