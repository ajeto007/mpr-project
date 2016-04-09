<?php

namespace App\Presenters;

use Nette;
use App\Model;


class CreateemployeePresenter extends BasePresenter
{

    public function renderDefault()
    {
        $this->template->anyVariable = 'any value';
    }

}
