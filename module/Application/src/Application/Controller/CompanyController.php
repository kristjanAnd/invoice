<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 21.04.15
 * Time: 18:05
 */

namespace Application\Controller;


use Application\Service\CompanyService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

class CompanyController extends AbstractActionController {

    const AUTHORIZE_CLASS = 'controller/Application\Controller\Company:';

    /**
     * @var CompanyService
     */
    protected $companyService;

    /**
     * @param CompanyService $companyService
     */
    public function setCompanyService(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function indexAction(){
        $translator = $this->serviceLocator->get('MvcTranslator');
        $user = $this->currentdata()->getCurrentUser();
        $company = $this->companyService->getCompanyById($this->params('id'));
        if(!$company || !$user || $user->getCompany() !== $company){
            return $this->notFoundAction();
        }
        $form = $this->serviceLocator->get('Application\Form\Subject')->init();
        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $this->companyService->saveCompany($company, new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Company.edit.successMessage'));
                return $this->redirect()->toRoute('company', ['id' => $company->getId()], true);
            }
        }
        $form->setFormValues($company);
        $view = new ViewModel();
        $view->company = $company;
        $view->user = $user;
        $view->form = $form;
        $view->messages = $this->flashMessenger()->getMessages();
        return $view;
    }


} 