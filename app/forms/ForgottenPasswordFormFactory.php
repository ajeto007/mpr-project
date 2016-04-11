<?php

namespace App\Forms;

use App\Model\Repository\EmployeeRepository;
use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class ForgottenPasswordFormFactory extends Nette\Object
{
    /** @var EmployeeRepository */
    private $employeeRepository;
    /** @var Nette\Mail\SmtpMailer */
    private $mailer;
    /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /**
     * ForgottenUserPasswordFormFactory constructor.
     * @param EmployeeRepository $userRepository
     * @param Nette\Mail\SmtpMailer $mailer
     * @param Nette\Application\LinkGenerator $linkGenerator
     */
    public function __construct(
        EmployeeRepository $userRepository,
        Nette\Mail\SmtpMailer $mailer,
        Nette\Application\LinkGenerator $linkGenerator
    ) {
        $this->employeeRepository = $userRepository;
        $this->mailer = $mailer;
        $this->linkGenerator = $linkGenerator;
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

        $form->addSubmit('submit', 'Potvrdit');

        $form->onSuccess[] = array($this, 'formSucceeded');
        return $form;
    }
    
    /**
     * @param Form $form
     * @param $values
     */
    public function formSucceeded(Form $form, $values)
    {
        $user = $this->employeeRepository->getOneByParameters(array('email' => $values['email']));
        if (is_null($user)) {
            $form->addError('nonExistUser');
            return;
        }

        $values['password'] = Nette\Utils\Random::generate();
        $mail = new Nette\Mail\Message();
        $mail->setFrom('NoReply MPR <noreply@mpr.cz>')
            ->addTo($values['email'])
            ->setSubject('Zapomenuté heslo do systému')
            ->setBody("Dobrý den,\nNové přihlašovací údaje do systému na stránce " .
                $this->linkGenerator->link('Homepage:') . " jsou:\n" .
                "Přihlašovací e-mail: " . $values['email'] . "\n" .
                "Vygenerované heslo: " . $values['password']);
        $values['password'] = Nette\Security\Passwords::hash($values['password']);
        unset($values['email']);

        try {
            $this->employeeRepository->updateWhere($values, array('id' => $user->getId()));
            $this->mailer->send($mail);
        } catch (\Exception $e) {
            $form->addError($e->getMessage());
            Debugger::log($e);
        }
    }
}
