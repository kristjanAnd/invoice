<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 8:33
 */

namespace Application\Controller;


use Application\Entity\Article;
use Application\Entity\Article\Brand;
use Application\Entity\Article\Category;
use Application\Entity\Article\Item;
use Application\Entity\Article\Service;
use Application\Entity\Unit;
use Application\Service\ArticleService;
use Application\Service\LanguageService;
use Application\Service\UnitService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ArticleController extends AbstractActionController {

    const AUTHORIZE_CLASS = 'controller/Application\Controller\Article:';
    const NAV_KEY_UNIT = 'unit';
    const NAV_KEY_CATEGORY = 'category';
    const NAV_KEY_BRAND = 'brand';
    const NAV_KEY_ITEM = 'item';
    const NAV_KEY_SERVICE = 'service';

    /**
     * @var ArticleService
     */
    protected $articleService;

    /**
     * @var UnitService
     */
    protected $unitService;

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

    public function itemAction(){
        $userData = $this->getUserData();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')->setCompany($userData->company)->init();
        if($this->request->isGet()){
            $filterForm->setData($this->request->getQuery());
            $filterData = $this->articleService->getFilterData($this->request->getQuery());
        }
        $items = $this->articleService->getItemsByCompany($userData->company, $filterData);

        $view = new ViewModel();
        $view->items = $this->getPaginatedResult($items, $this->params('page'));
        $view->navKey = self::NAV_KEY_ITEM;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->statuses = $this->articleService->getArticleStatusSelect();
        $view->filterForm = $filterForm;
        return $view;
    }

    public function addItemAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Article')->setCompany($userData->company)->init();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $item = $this->articleService->saveArticle(new Item($userData), new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Article.item.add.successMessage'));
                return $this->redirect()->toRoute('item', [], true);
            }
        }
        $view->form = $form;
        $view->navKey = self::NAV_KEY_ITEM;
        return $view;
    }

    public function editItemAction(){
        $item = $this->articleService->getArticleById($this->params('id'));
        $userData = $this->getUserData();
        if(!$item || !$item instanceof Item || $item->getCompany() !== $userData->company){
            return $this->notFoundAction();
        }
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Article')->setCompany($userData->company)->init();
        $form->setFormValues($item);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $item = $this->articleService->saveArticle($item, new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Article.item.edit.successMessage'));
                return $this->redirect()->toRoute('item', [], true);
            }
        }
        $view->navKey = self::NAV_KEY_ITEM;
        $view->form = $form;
        return $view;
    }

    public function deleteItemAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function unitAction(){
        $userData = $this->getUserData();
        $form = $this->getServiceLocator()->get('Application\Form\Unit')->init();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')->init();
        if($this->request->isGet()){
            $filterForm->setData($this->request->getQuery());
            $filterData = $this->unitService->getFilterData($this->request->getQuery());
        }
        $units = $this->unitService->getUnitsByCompany($userData->company, $filterData);

        $view = new ViewModel();
        $view->units = $this->getPaginatedResult($units, $this->params('page'));
        $view->navKey = self::NAV_KEY_UNIT;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->form = $form;
        $view->statuses = $this->articleService->getUnitStatusSelect();
        $view->filterForm = $filterForm;
        return $view;
    }

    public function addUnitAction(){
        $form = $this->getServiceLocator()->get('Application\Form\Unit')->init();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $userData = $this->getUserData();
                $unit = $this->unitService->saveUnit(new Unit($userData), new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Article.unit.add.successMessage'));
                return $this->redirect()->toRoute('unit', [], true);
            }
            $this->flashMessenger()->addErrorMessage($translator->translate('Article.unit.add.errorMessage'));
            return $this->redirect()->toRoute('unit', [], true);
        }
        return $this->notFoundAction();
    }

    public function editUnitAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $unit = $this->unitService->getUnitById($this->request->getQuery()->id);
            $userData = $this->getUserData();
            if($unit && $unit->getCompany() == $userData->company){
                $this->unitService->saveUnit($unit, $this->request->getQuery());
                return new JsonModel(array('error' => 0));
            }
        }
        return $this->response;
    }

    public function deleteUnitAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function serviceAction(){
        $userData = $this->getUserData();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')->init();
        if($this->request->isGet()){
            $filterForm->setData($this->request->getQuery());
            $filterData = $this->articleService->getFilterData($this->request->getQuery());
        }
        $services = $this->articleService->getServicesByCompany($userData->company, $filterData);

        $view = new ViewModel();
        $view->services = $this->getPaginatedResult($services, $this->params('page'));
        $view->navKey = self::NAV_KEY_SERVICE;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->statuses = $this->articleService->getArticleStatusSelect();
        $view->filterForm = $filterForm;
        return $view;
    }

    public function addServiceAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Article')->setCompany($userData->company)->init();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $service = $this->articleService->saveArticle(new Service($userData), new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Article.service.add.successMessage'));
                return $this->redirect()->toRoute('service', [], true);
            }
        }
        $view->form = $form;
        $view->navKey = self::NAV_KEY_SERVICE;
        return $view;
    }

    public function editServiceAction(){
        $service = $this->articleService->getArticleById($this->params('id'));
        $userData = $this->getUserData();
        if(!$service || !$service instanceof Service || $service->getCompany() !== $userData->company){
            return $this->notFoundAction();
        }
        $view = new ViewModel();
        $form = $this->getServiceLocator()->get('Application\Form\Article')->setCompany($userData->company)->init();
        $form->setFormValues($service);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $service = $this->articleService->saveArticle($service, new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Article.service.edit.successMessage'));
                return $this->redirect()->toRoute('service', [], true);
            }
        }
        $view->navKey = self::NAV_KEY_SERVICE;
        $view->form = $form;
        return $view;
    }

    public function deleteServiceAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function categoryAction(){
        $userData = $this->getUserData();
        $form = $this->getServiceLocator()->get('Application\Form\Category')->init();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')->init();
        if($this->request->isGet()){
            $filterForm->setData($this->request->getQuery());
            $filterData = $this->articleService->getFilterData($this->request->getQuery());
        }
        $categories = $this->articleService->getCategoriesByCompany($userData->company, $filterData);

        $view = new ViewModel();
        $view->categories = $this->getPaginatedResult($categories, $this->params('page'));
        $view->navKey = self::NAV_KEY_CATEGORY;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->form = $form;
        $view->statuses = $this->articleService->getArticleCategoryStatusSelect();
        $view->filterForm = $filterForm;
        return $view;
    }

    public function addCategoryAction(){
        $form = $this->getServiceLocator()->get('Application\Form\Category')->init();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $userData = $this->getUserData();
                $category = $this->articleService->saveCategory(new Category($userData), new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Article.category.add.successMessage'));
                return $this->redirect()->toRoute('category', [], true);
            }
            $this->flashMessenger()->addErrorMessage($translator->translate('Article.category.add.errorMessage'));
            return $this->redirect()->toRoute('category', [], true);
        }
        return $this->notFoundAction();
    }

    public function editCategoryAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $category = $this->articleService->getCategoryById($this->request->getQuery()->id);
            $userData = $this->getUserData();
            if($category && $category->getCompany() == $userData->company){
                $this->articleService->saveCategory($category, $this->request->getQuery());
                return new JsonModel(array('error' => 0));
            }
        }
        return $this->response;
    }

    public function deleteCategoryAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function brandAction(){
        $userData = $this->getUserData();
        $form = $this->getServiceLocator()->get('Application\Form\Brand')->init();
        $filterForm = $this->getServiceLocator()->get('Application\Form\Filter')->init();
        if($this->request->isGet()){
            $filterForm->setData($this->request->getQuery());
            $filterData = $this->articleService->getFilterData($this->request->getQuery());
        }
        $brands = $this->articleService->getBrandsByCompany($userData->company, $filterData);
        $view = new ViewModel();
        $view->brands = $this->getPaginatedResult($brands, $this->params('page'));
        $view->navKey = self::NAV_KEY_BRAND;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->form = $form;
        $view->statuses = $this->articleService->getArticleBrandStatusSelect();
        $view->filterForm = $filterForm;
        return $view;
    }

    public function addBrandAction(){
        $form = $this->getServiceLocator()->get('Application\Form\Brand')->init();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            $translator = $this->getTranslator();
            if($form->isValid()){
                $userData = $this->getUserData();
                $brand = $this->articleService->saveBrand(new Brand($userData), new Parameters($form->getData()));
                $this->flashMessenger()->addMessage($translator->translate('Article.brand.add.successMessage'));
                return $this->redirect()->toRoute('brand', [], true);
            }
            $this->flashMessenger()->addErrorMessage($translator->translate('Article.brand.add.errorMessage'));
            return $this->redirect()->toRoute('brand', [], true);
        }
        return $this->notFoundAction();
    }

    public function editBrandAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $brand = $this->articleService->getBrandById($this->request->getQuery()->id);
            $userData = $this->getUserData();
            if($brand && $brand->getCompany() == $userData->company){
                $this->articleService->saveBrand($brand, $this->request->getQuery());
                return new JsonModel(array('error' => 0));
            }
        }
        return $this->response;
    }

    public function deleteBrandAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function getArticleSelectAction(){
        if ($this->request->isGet() && $this->request->isXmlHttpRequest()) {
            $userData = $this->getUserData();
            $type = $this->request->getQuery()->type;
            $category = $this->articleService->getCategoryById($this->request->getQuery()->category);
            $brand = $this->articleService->getBrandById($this->request->getQuery()->brand);
            $params = new Parameters();
            $params->statuses = array(Article::STATUS_ACTIVE);
            if($category){
                $params->category = $category;
            }
            if($brand){
                $params->brand = $brand;
            }

            $articles = $type == Article::ARTICLE_TYPE_SERVICE ? $this->articleService->getServicesByCompany($userData->company, $params) : $this->articleService->getItemsByCompany($userData->company, $params);
            $view = new ViewModel();
            $view->setTemplate('form/select/article');
            $view->setTerminal(true);
            $view->articles = $articles;
            $view->emptyOption = $this->getTranslator($this->request->getQuery()->locale)->translate('ArticleAdd.form.article.emptyOption');

            return $view;

        }
        return $this->response;
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

    private function addTestData(){
        $params = new Parameters();
        for($i = 0; $i < 40; $i++){
            $params->name = 'Toode-' . $i;
            $params->code = 'Kood-' . $i;
            $params->quantity = 1;
            $params->salePrice = 10 + $i;
            $params->unit = 29;
            $params->status = Item::STATUS_ACTIVE;
        }
    }
} 