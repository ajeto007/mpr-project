<?php
namespace App\Forms;

use App\Model\Entity\Address;
use App\Model\Repository\ClientRepository;
use Nette;
use Nette\Application\UI\Form;
use App\Model\Entity\Client;
use Tracy\Debugger;

class ClientFormFactory extends Nette\Object
{
    /** @var ClientRepository */
    public $clientRepository;

    /** @var ClientFormFactoryForm @inject */
    public $clientFormFactory;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function create()
    {
        $form = new Form;

        $form->addText('companyName', 'Jméno firmy')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        $form->addText('contactName', 'Kontaktní osoba')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        $form->addText('phone', 'Telefon')
            ->setAttribute('class', 'form-control')
            ->setOption('description', '123456789');
        $form->addText('email', 'Email')
            ->addRule(Form::PATTERN, 'Zadej validní email', '[a-zA-Z0-9\.\-_]+@[0-9a-zA-Z\.\-]+\.[a-z]+')
            ->setAttribute('class', 'form-control');
        $form->addText('street', 'Ulice, Č.P')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);
        $form->addText('city', 'Město')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);
        $form->addText('postcode', 'PSČ')
            ->setAttribute('class', 'form-control')
            ->addRule(Form::FILLED);
        $form->addText('ico', 'IČO')
            ->setAttribute('class', 'form-control');

        $form->addText('dic', 'DIČ')
            ->setAttribute('class', 'form-control');
        //
        $form->addSubmit('submit', 'Odeslat')
            ->setAttribute('class', 'btn btn-default');

        $form->addHidden('id');

        $form->onSuccess[] = array($this, 'processForm');
        return $form;
    }

    public function processForm(Form $form, $values)
    {
        if($values->id) {
            $client = $this->clientRepository->getById($values->id);
            $address = $client->getAddress();
        } else {
            $address = new Address();
            $client = new Client();
        }

        $address->setStreet($values->street);
        $address->setPostcode($values->postcode);
        $address->setCity($values->city);

        $client->setEmail($values->email);
        $client->setCompanyName($values->companyName);
        $client->setAddress($address);
        $client->setPhone($values->phone);
        $client->setIco($values->ico);
        $client->setDic($values->dic);
        $client->setContactName($values->contactName);

        try {
            if ($values->id) {
                $this->clientRepository->update($client);
            } else {
                $this->clientRepository->insert($client);
            }
        } catch (\Exception $e) {
            Debugger::log($e);
            $form->addError($e->getMessage());
        }
    }
}