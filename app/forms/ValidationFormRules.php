<?php

namespace App\Forms;

use Nette;
use Nette\Forms\IControl;

class ValidationFormRules extends Nette\Object
{
    const IS_DATE = 'App\Forms\ValidationFormRules::validateDate';

    public static function validateDate(IControl $control)
    {
        $date = $control->getValue();

        $result = true;

        try {
            $check = new \DateTime($date);
        } catch(\Exception $e){
            $result=false;
        }

        return $result;
    }
}