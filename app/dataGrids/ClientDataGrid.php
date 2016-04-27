<?php

namespace App\DataGrids;

use App\Model\Repository\ClientRepository;
use Nette\Object;
use Ublaboo\DataGrid\DataGrid;

class ClientDataGrid extends Object
{
    /** @var ClientRepository */
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function create()
    {
        $grid = new DataGrid();

        $grid->setRememberState(FALSE);

        $grid->setDataSource($this->clientRepository->getQB());

        $grid->addColumnText('companyName', 'Název')
            ->setSortable();

        $grid->addFilterText('companyName', 'Název');

        $grid->addColumnText('contactName', 'Kontaktní osoba')
            ->setSortable();

        $grid->addFilterText('contactName', 'Kontaktní osoba');

        $grid->addColumnText('email', 'E-mail')
            ->setSortable();

        $grid->addFilterText('email', 'E-mail');

        $grid->addColumnText('phone', 'Telefon')
            ->setSortable();

        $grid->addFilterText('phone', 'Telefon');

        $grid->addAction('edit', 'Upravit', 'Clients:edit')
            ->setIcon('pencil')
            ->setClass('btn btn-xs btn-success');

        $grid->addAction('delete', 'Smazat', 'Clients:delete')
            ->setIcon('trash')
            ->setClass('btn btn-xs btn-danger')
            ->setConfirm('Chcete opravdu odstranit klienta %s?', 'companyName');

        $grid->setItemsDetail();
        $grid->setTemplateFile(__DIR__ . '/ClientDataGrid.latte');

        return $grid;
    }
}