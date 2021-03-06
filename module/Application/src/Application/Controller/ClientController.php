<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 11:17
 */

namespace Application\Controller;


use Application\Entity\Subject\Client;
use Application\Service\ClientService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ClientController extends AbstractActionController {

    const AUTHORIZE_CLASS = 'controller/Application\Controller\Client:';
    const NAV_KEY_CLIENT = 'client';

    /**
     * @var ClientService
     */
    protected $clientService;

    /**
     * @param ClientService $clientService
     */
    public function setClientService(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function clientAction(){
        $userData = $this->getUserData();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')->setCompany($userData->company)->init();
        if($this->request->isGet()){
            $filterForm->setData($this->request->getQuery());
            $filterData = $this->clientService->getFilterData($this->request->getQuery(), $userData->user);
        }
        $clients = $this->clientService->getCompanyClients($userData->company, $filterData);

        $view = new ViewModel();
        $view->clients = $this->getPaginatedResult($clients, $this->params('page'));
        $view->navKey = self::NAV_KEY_CLIENT;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->statuses = $this->clientService->getClientStatusSelect();
        $view->filterForm = $filterForm;
        $view->user = $userData->user;
        return $view;
    }

    public function addClientAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Subject\Client')->setCompany($userData->company)->init();
        $form->setDefaultClientUser($userData->user);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $params = $this->request->getPost();
                $client = $this->clientService->saveClient(new Client($userData), $params);
                if($this->checkIfModalRequest()){
                    return $this->getClientSelectView($client, $params);
                }
                $this->flashMessenger()->addMessage($translator->translate('Client.add.successMessage'));
                return $this->redirect()->toRoute('client', [], true);
            }
            if($this->checkIfModalRequest()){
                return new JsonModel(array('error' => 1));
            }
        }
        $view->form = $form;
        $view->navKey = self::NAV_KEY_CLIENT;
        return $view;
    }

    private function getClientSelectView(Client $client, Parameters $data){
        $userData = $this->getUserData();
        $clients = $this->clientService->getCompanyActiveClients($userData->company);
        $view = new ViewModel();
        $view->setTemplate('form/select/client');
        $view->setTerminal(true);
        $view->clients = $clients;
        $view->client = $client;
        $view->emptyOption = $this->getTranslator(isset($data->locale) ? $data->locale : null)->translate('Invoice.form.client.emptyOption');
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $result = $this->clientService->getClientDataForAjax($client->getId(), $client);
        $result['html'] = $viewRender->render($view);
        return new JsonModel($result);
    }

    public function editClientAction(){
        $client = $this->clientService->getClientById($this->params('id'));
        $userData = $this->getUserData();
        if(!$client || $client->getCompany() !== $userData->company){
            return $this->notFoundAction();
        }
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Subject\Client')->setCompany($userData->company)->setClient($client)->init();
        $form->setFormValues($client);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $client = $this->clientService->saveClient($client, new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Client.edit.successMessage'));
                return $this->redirect()->toRoute('client', [], true);
            }
        }
        $view->navKey = self::NAV_KEY_CLIENT;
        $view->form = $form;
        $view->client = $client;
        return $view;
    }

    private function getUserData(){
        $data = new Parameters();
        $user = $this->currentdata()->getCurrentUser();
        $company = $user->getCompany();
        $data->company = $company;
        $data->user = $user;
        return $data;
    }

    private function getPaginatedResult(array $collection, $currentPageNumber, $pageRange = 10, $cntPerPage = 10){
        $paginated = new Paginator(new ArrayAdapter($collection));
        $paginated->setCurrentPageNumber($currentPageNumber);
        $paginated->setPageRange($pageRange);
        $paginated->setItemCountPerPage($cntPerPage);

        return $paginated;
    }

    private function getTranslator($locale = null){
        $translator =  $this->serviceLocator->get('MvcTranslator');
        if($locale){
            $translator->setLocale($locale);
        }
        return $translator;
    }

    private function checkIfModalRequest(){
        return (isset($_GET['modal']) && $_GET['modal'] == 1) ? true : false;
    }
} 