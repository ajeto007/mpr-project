<?php

namespace App\Presenters;

use App\Forms\ProjectFormFactory;
use App\Model\Repository\ProjectRepository;
use Nette;
use App\Model;
use App\DataGrids\ProjectDataGrid;

class ProjectsPresenter extends BasePresenter
{
    /** @var ProjectFormFactory @inject */
    public $projectFormFactory;

    /** @var ProjectRepository @inject */
    public $projectRepository;

    /** @var ProjectDataGrid @inject */
    public $projectDataGrid;

    public function renderDefault()
    {
        $this->template->projects = $this->projectRepository->getAll();
    }

    public function renderDetail($id)
    {
        $this->template->project = $this->projectRepository->getById($id);
    }

    public function actionDelete($id)
    {
        $project = $this->projectRepository->getById($id);

        if (is_null($project)) {
            throw new Nette\Application\BadRequestException();
        } elseif ($this->user->isInRole('vedouci') && $this->user->id != $project->getLeader()->getId()) {
            throw new Nette\Application\ForbiddenRequestException();
        }

        try {
            $this->projectRepository->delete($project);
            $this->flashMessage('Projekt ' . $project->getName() . ' smazán');
        }
        catch (\Exception $e) {
            $this->flashMessage('Projekt se nepodařilo smazat.', 'danger');
        }
        $this->redirect('default');
    }

    public function actionEdit($id)
    {
        $project = $this->projectRepository->getById($id);

        if (is_null($project)) {
            throw new Nette\Application\BadRequestException();
        } elseif ($this->user->isInRole('vedouci') && $this->user->id != $project->getLeader()->getId()) {
            throw new Nette\Application\ForbiddenRequestException();
        }

        $data = $project->getAsArray();
        $data['fromDate'] = $data['fromDate']->format('m/d/Y h:i A');
        $data['toDate'] = $data['toDate']->format('m/d/Y h:i A');
        if ($this->user->isInRole('vedouci')) {
            $this['projectForm']['leader']->setDisabled();
        }
        $this['projectForm']->setDefaults($data);
    }

    protected function createComponentProjectForm()
    {
        $control = $this->projectFormFactory->create();
        $control->onSuccess[] = array($this, 'formSuccess');
        $control->onError[] = array($this, 'formError');
        return $control;
    }

    public function formSuccess($form)
    {
        $values = $form->getValues();

        $this->flashMessage('Projekt ' . $values->name . ' byl úspěšně '.(($values->id) ? "aktualizován" : "přidán"), 'success');
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

    protected function createComponentProjectDataGrid()
    {
        $control = $this->projectDataGrid->create();
        return $control;
    }
}
