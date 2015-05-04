<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 18:50
 */

namespace Application\Controller;


use Application\Service\InvoiceService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

class InvoiceController extends AbstractActionController {

    const AUTHORIZE_CLASS = 'controller/Application\Controller\Invoice:';
    const NAV_KEY_INVOICE = 'invoice';

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @param InvoiceService $invoiceService
     */
    public function setInvoiceService(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function invoiceAction(){
        $userData = $this->getUserData();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')->setCompany($userData->company)->init();
        if($this->request->isGet()){
            $filterForm->setData($this->request->getQuery());
            $filterData = $this->invoiceService->getFilterData($this->request->getQuery(), $userData->user);
        }
        $invoices = $this->invoiceService->getCompanyInvoices($userData->company, $filterData);

        $view = new ViewModel();
        $view->invoices = $this->getPaginatedResult($invoices, $this->params('page'));
        $view->navKey = self::NAV_KEY_INVOICE;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->statuses = $this->invoiceService->getInvoiceStatusSelect();
        $view->filterForm = $filterForm;
        $view->user = $userData->user;
        return $view;
    }

    public function addInvoiceAction(){
        $view = new ViewModel();
        return $view;
    }

    public function editInvoiceAction(){
        $view = new ViewModel();
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

    private function getTranslator(){
        return $this->serviceLocator->get('MvcTranslator');
    }

} 