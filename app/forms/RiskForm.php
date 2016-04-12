<?php

namespace App\Forms;

use App\Model\Repository\RiskRepository;
use App\Model\Repository\ProjectRepository;
use App\Model\Repository\CategoryRepository;
use Nette;
use Nette\Application\UI\Form;
use App\Model\Entity\Category;
use App\Model\Entity\Project;
use App\Model\Entity\Risk;
use Tracy\Debugger;

class RiskForm extends Nette\Object
{
    /** @var RiskRepository */
    public $riskRepository;

    /** @var ProjectRepository */
    public $projectRepository;
    
    /** @var CategoryRepository */
    public $categoryRepository;

    public function __construct(RiskRepository $riskRepository, CategoryRepository $categoryRepository, ProjectRepository $projectRepository)
    {
        $this->riskRepository = $riskRepository;
        $this->projectRepository = $projectRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function create()
    {
        $categories = array();
        $projects = array();

        foreach($this->categoryRepository->getAll() as $c) {
            $categories[$c->getId()] = $c->getName();
        }

        foreach($this->projectRepository->getAll() as $p) {
            $projects[$p->getId()] = $p->getName();
        }

        $form = new Form;

        $form->addSelect('category', 'Kategorie', $categories)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addSelect('project', 'Project', $projects)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addText('name', 'Jméno')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addText('description', 'Popis')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addText('threat', 'Hrozba')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addText('starter', 'Spouštěč')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        $form->addText('reaction', 'Reakce')
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        $form->addSelect('impacts', 'Dopady', Risk::$impactsEnum)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        $form->addSelect('state', 'Stav', Risk::$stateEnum)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');
        $form->addSelect('probability', 'Pravděpodobnost', Risk::$probabilityEnum)
            ->addRule(Form::FILLED)
            ->setAttribute('class', 'form-control');

        $form->addSubmit('submit', 'Odeslat')
            ->setAttribute('class', 'btn btn-default');

        $form->addHidden('id');

        $form->onSuccess[] = array($this, 'processForm');
        return $form;
    }

    public function processForm(Form $form, $values)
    {
        if($values->id) {
            $risk = $this->riskRepository->getById($values->id);
            $risk->setModified(new \DateTime('NOW'));

            if($values->state == 'aktivni' && $risk->getState() != 'aktivni') {
                $risk->setActivated(new \DateTime('NOW'));
            }
        } else {
            $risk = new Risk();
            $risk->setCreated(new \DateTime('NOW'));
            $risk->setModified(new \DateTime('NOW'));

            if($values->state == 'aktivni') {
                $risk->setActivated(new \DateTime('NOW'));
            }
        }

        $risk->setCategory($this->categoryRepository->getById($values->category));
        $risk->setProject($this->projectRepository->getById($values->project));
        $risk->setName($values->name);
        $risk->setDescription($values->description);
        $risk->setThreat($values->threat);
        $risk->setStarter($values->starter);
        $risk->setReaction($values->reaction);
        $risk->setImpacts($values->impacts);
        $risk->setState($values->state);
        $risk->setProbability($values->probability);
        //
        try {
            if ($values->id) {
                $this->riskRepository->update($risk);
            } else {
                $this->riskRepository->insert($risk);
            }
        } catch (\Exception $e) {
            Debugger::log($e);
            $form->addError($e->getMessage());
        }
    }
}
