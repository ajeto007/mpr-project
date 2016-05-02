<?php

namespace App\Forms;

use App\Model\Repository\ProjectRepository;
use App\Model\Repository\ClientRepository;
use App\Model\Repository\EmployeeRepository;
use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use App\Model\Entity\Project;

class ProjectFormFactory extends Nette\Object
{
    /** @var EmployeeRepository */
    public $employeeRepository;

    /** @var ClientRepository */
    public $clientRepository;

    /** @var ProjectRepository */
    public $projectRepository;

    public function __construct(EmployeeRepository $employeeRepository, ClientRepository $clientRepository, ProjectRepository $projectRepository)
    {
        $this->employeeRepository = $employeeRepository;
        $this->clientRepository = $clientRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @return Form
     */
    public function create()
    {

        $admins = EmployeeRepository::getIdIndexedArrayOfNames($this->employeeRepository->getByParameters(array('role' => 'admin')));
        $heads = EmployeeRepository::getIdIndexedArrayOfNames($this->employeeRepository->getByParameters(array('role' => 'vedouci')));
        foreach ($admins as $id => $admin) {
            $heads[$id] = $admin;
        }
        $employees =  EmployeeRepository::getIdIndexedArrayOfNames($this->employeeRepository->getAll());
        $clients = ClientRepository::getIdIndexedArrayOfNames($this->clientRepository->getAll());

        $form = new Form;

        $form->addText('name', 'Jméno')
            ->setRequired('Vložte prosím jméno projektu.')
            ->setAttribute('placeholder', 'Vyplnit');

        $form->addTextArea('description', 'Popis')
            ->setAttribute('rows', 3)
            ->setAttribute('placeholder', 'Vyplnit');

        $form->addSelect('leader', 'Vedoucí', $heads)
            ->setRequired('Vyberte prosím vedoucího projektu.');

        $form->addSelect('client', 'Klient', $clients)
            ->setRequired('Vyberte prosím zadavatele (klienta) pro tento projekt.');

        $form->addText('fromDate', 'Datum začátku')
            ->addRule(Form::PATTERN, 'Zadejte prosím validní datum', '[0-9]{2}\/[0-9]{2}\/[0-9]{4}')
            ->addRule(ValidationFormRules::IS_DATE, 'Zadané datum není validní.')
            ->setRequired('Vložte prosím datum začátku projektu.')
            ->setOption('description', '(mm/dd/yyyy)');

        $form->addText('toDate', 'Datum konce')
            ->addRule(Form::PATTERN, 'Zadejte prosím validní datum', '[0-9]{2}\/[0-9]{2}\/[0-9]{4}')
            ->addRule(ValidationFormRules::IS_DATE, 'Zadané datum není validní.')
            ->setRequired('Vložte prosím datum konce projektu.')
            ->setOption('description', '(mm/dd/yyyy)');

        $form->addCheckboxList('employees', 'Uživatelé', $employees);

        $form->addHidden('id');

        $form->addSubmit('submit', 'Uložit projekt');

        $form->onSuccess[] = array($this, 'formSucceeded');
        return $form;
    }

    public function formSucceeded(Form $form, $values)
    {
        $dateFrom = new \DateTime($values->fromDate);
        $dateTo = new \DateTime($values->toDate);
        if ($dateFrom>$dateTo)
        {
            $form->addError("Projekt končí dříve než začíná");
            return;
        }

        $allEmployees = array();
        foreach ($this->employeeRepository->getAll() as $e) {
            $allEmployees[$e->getId()] = $e;
        }

        if ($values->id)
        {
            $project = $this->projectRepository->getById($values->id);
            $project->getEmployees()->clear();
        }
        else
        {
            $project = new Project();
        }
        $project->setName($values->name);
        $project->setDescription($values->description);
        $project->setClient($this->clientRepository->getById($values->client));
        $project->setLeader($allEmployees[$values->leader]);
        $project->setDescription($values->description);
        $project->setFromDate($dateFrom);
        $project->setToDate($dateTo);
        $employees = $project->getEmployees();
        foreach ($values->employees as $e) {
            $employees->add($allEmployees[$e]);
        }

        try {
            if ($values->id)
            {
                $this->projectRepository->update($project);
            }
            else
            {
                $this->projectRepository->insert($project);
            }
        } catch (\Exception $e) {
            Debugger::log($e);
            $form->addError($e->getMessage());
        }
    }
}