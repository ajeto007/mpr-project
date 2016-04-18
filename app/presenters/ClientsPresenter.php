<?php
namespace App\Presenters;

use Nette;
use App\Model;
use App\Forms\ClientFormFactory;
use App\Model\Repository\ClientRepository;

class ClientsPresenter extends BasePresenter
{
    /** @var ClientFormFactory @inject */
    public $clientFormFactory;

    /** @var ClientRepository @inject */
    public $clientRepository;

    public function renderDefault()
    {
        $this->template->clients = $this->clientRepository->getAll();
    }

    public function actionEdit($id)
    {
        $client = $this->clientRepository->getById($id);
        $address = $client->getAddress()->getAsArray();
        unset($address['id']); //tady dochazi ke klicu 'id', id adresy nepotrebujeme
        $data = $client->getAsArray();
        $data = array_merge($data, $address);
        $this['clientForm']->setDefaults($data);
    }

    public function actionDelete($id)
    {
        try
        {
            $this->clientRepository->deleteWhere(array("id" => $id));
            $this->flashMessage('Klient smazán');
        }
        catch (\Exception $e)
        {
            $this->flashMessage('Klienta se nepodařilo smazat: ' . $e->getMessage(), 'danger');
        }
        $this->redirect('default');
    }

    public function formSuccess($form)
    {
        $values = $form->getValues();

        if($values->id) {
            $text = 'aktualizován';
        }
        else {
            $text = 'přidán';
        }

        $this->flashMessage('Klient ' . $values->companyName . ' úspěšně ' . $text, 'success');
        $this->redirect('default');
    }

    public function formError($form)
    {
        $errors = "";
        foreach($form->getErrors() as $error) {
            $errors .= " ".$error;
        }

        $this->flashMessage('Při ukládání došlo k chybě:'.$errors, 'danger');
    }

    protected function createComponentClientForm()
    {
        $control = $this->clientFormFactory->create();
        $control->onSuccess[] = array($this, 'formSuccess');
        $control->onError[] = array($this, 'formError');
        return $control;
    }
}