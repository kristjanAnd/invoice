<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 18:50
 */

namespace Application\Controller;


use Application\Service\ArticleService;
use Application\Service\InvoiceService;
use Application\Service\LanguageService;
use Application\Service\UnitService;
use Application\Service\VatService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

class InvoiceController extends AbstractActionController {

    const AUTHORIZE_CLASS = 'controller/Application\Controller\Invoice:';
    const NAV_KEY_INVOICE = 'invoice';
    const NAV_KEY_INVOICE_SETTING = 'invoice-setting';

    /**
     * @var InvoiceService
     */
    protected $invoiceService;
    /**
     * @var ArticleService
     */
    protected $articleService;
    /**
     * @var UnitService
     */
    protected $unitService;
    /**
     * @var VatService
     */
    protected $vatService;

    /**
     * @param VatService $vatService
     */
    public function setVatService(VatService $vatService)
    {
        $this->vatService = $vatService;
    }

    /**
     * @param UnitService $unitService
     */
    public function setUnitService(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * @param ArticleService $articleService
     */
    public function setArticleService(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @param InvoiceService $invoiceService
     */
    public function setInvoiceService(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function invoiceAction(){
        $userData = $this->getUserData();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')
            ->setFilterDateFormat(LanguageService::getCurrentLanguageCode($this->getServiceLocator()))
            ->setCompany($userData->company)
            ->init();
        $filterForm->setDefaultFilterDates();
        if($this->request->isGet() && count($this->request->getQuery()) > 0){
            $filterForm->setData($this->request->getQuery());
            if($filterForm->isValid()){
                $filterData = $this->invoiceService->getFilterData(new Parameters($filterForm->getData()), $userData->user, $filterForm->getFilterDateFormat());
            }
        } else {
            $filterData = $this->invoiceService->getDefaultFilterData($filterForm, $userData->user);
        }
        $invoices = $this->invoiceService->getCompanyInvoices($userData->company, $filterData);

        $view = new ViewModel();
        $view->invoices = $this->getPaginatedResult($invoices, $this->params('page'));
        $view->navKey = self::NAV_KEY_INVOICE;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->statuses = $this->invoiceService->getInvoiceStatusSelect();
        $view->paymentStatuses = $this->invoiceService->getInvoiceStatusSelect();
        $view->filterForm = $filterForm;
        $view->user = $userData->user;
        return $view;
    }

    public function addInvoiceAction(){
        $userData = $this->getUserData();
        $currentLanguageCode = LanguageService::getCurrentLanguageCode($this->getServiceLocator());
        $defaultDateFormat = LanguageService::getDateFormatByLanguageCode($currentLanguageCode);
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Document\Invoice')->setCompany($userData->company)->setDateFormat($defaultDateFormat)->setLanguageCode($currentLanguageCode)->init();
        $form->setDefaultUser($userData->user);
        $form->setDefaultData($userData->invoiceSetting);
        $addArticleForm = $this->getServiceLocator()->get('Application\Form\AddArticle')->setCompany($userData->company)->init();
        $rowForm = $this->getServiceLocator()->get('Application\Form\DocumentRow\InvoiceRow')->setCompany($userData->company)->init();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
//                $client = $this->clientService->saveClient(new Client($userData), new Parameters($form->getData()));
//                $this->flashMessenger()->addMessage($translator->translate('Client.add.successMessage'));
//                return $this->redirect()->toRoute('client', [], true);
            }
        }
        $view->form = $form;
        $view->navKey = self::NAV_KEY_INVOICE;
        $view->addArticleFrom = $addArticleForm;
        $view->rowForm = $rowForm;
        return $view;
    }

    public function editInvoiceAction(){
        $view = new ViewModel();
        return $view;
    }

    public function invoiceSettingAction(){
        $view = new ViewModel();
        $userData = $this->getUserData();
        $companyInvoiceSetting = $this->invoiceService->getInvoiceSettingByCompany($userData->company, $userData->user);
        $form = $this->getServiceLocator()->get('Application\Form\DocumentSetting\InvoiceSetting')->setCompany($userData->company)->init();
        $form->setFormValues($companyInvoiceSetting);

        $view->form = $form;
        $view->navKey = self::NAV_KEY_INVOICE_SETTING;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        return $view;
    }

    public function editInvoiceSettingAction(){
        $userData = $this->getUserData();
        $companyInvoiceSetting = $this->invoiceService->getInvoiceSettingByCompany($userData->company, $userData->user);
        $form = $this->getServiceLocator()->get('Application\Form\DocumentSetting\InvoiceSetting')->setCompany($userData->company)->init();
        if($this->request->isPost()){
            $translator = $this->getTranslator();
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $this->invoiceService->saveInvoiceSetting($companyInvoiceSetting, $this->request->getPost());
                $this->flashMessenger()->addMessage($translator->translate('Invoice.editInvoiceSetting.successMessage'));
            } else {
                $this->flashMessenger()->addErrorMessage($translator->translate('Invoice.editInvoiceSetting.errorMessage'));
            }
        }
        return $this->redirect()->toRoute('invoice-setting', [], true);
    }

    public function addArticleAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $userData = $this->getUserData();
            $type = $this->request->getQuery()->type;
            $invoice = $this->invoiceService->getInvoiceById($this->request->getQuery()->invoiceId);
            $article = $this->articleService->getArticleById($this->request->getQuery()->articleId);
            $rowDto = $this->invoiceService->createInvoiceRowDto($invoice, $article);
            $units = $this->unitService->getActiveCompanyUnits($userData->company);
            $vats = $this->vatService->getCompanyActiveVats($userData->company);

            $view = new ViewModel();
            $view->setTemplate('application/invoice/partial/dynamic-row');
            $view->setTerminal(true);
            $view->dto = $rowDto;
            $view->vats = $vats;
            $view->units = $units;
            $view->vatEmptyOption = $this->getTranslator($this->request->getQuery()->locale)->translate('DocumentRowForm.form.vat.emptyOption');
            $view->unitEmptyOption = $this->getTranslator($this->request->getQuery()->locale)->translate('DocumentRowForm.form.unit.emptyOption');

            return $view;

        }
        return $this->response;
    }

    private function getUserData(){
        $data = new Parameters();
        $user = $this->currentdata()->getCurrentUser();
        $company = $user->getCompany();
        $companyInvoiceSetting = $this->invoiceService->getInvoiceSettingByCompany($company, $user);
        $data->company = $company;
        $data->user = $user;
        $data->invoiceSetting = $companyInvoiceSetting;
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

} 