<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 8:33
 */

namespace Application\Controller;


use Application\Entity\Article\Brand;
use Application\Entity\Article\Category;
use Application\Entity\Unit;
use Application\Service\ArticleService;
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
        $view = new ViewModel();
        return $view;
    }

    public function addItemAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function editItemAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function deleteItemAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function unitAction(){
        $userData = $this->getUserData();
        $units = $this->unitService->getUnitsByCompany($userData->company);
        $form = $this->getServiceLocator()->get('Application\Form\Unit')->init();
        $view = new ViewModel();
        $view->units = $this->getPaginatedResult($units, $this->params('page'));
        $view->navKey = self::NAV_KEY_UNIT;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->form = $form;
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
        $view = new ViewModel();
        return $view;
    }

    public function addServiceAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function editServiceAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function deleteServiceAction(){
        $userData = $this->getUserData();
        $view = new ViewModel();
        return $view;
    }

    public function categoryAction(){
        $userData = $this->getUserData();
        $categories = $this->articleService->getCategoriesByCompany($userData->company);
        $form = $this->getServiceLocator()->get('Application\Form\Category')->init();
        $view = new ViewModel();
        $view->categories = $this->getPaginatedResult($categories, $this->params('page'));
        $view->navKey = self::NAV_KEY_CATEGORY;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->form = $form;
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
        $brands = $this->articleService->getBrandsByCompany($userData->company);
        $form = $this->getServiceLocator()->get('Application\Form\Brand')->init();
        $view = new ViewModel();
        $view->brands = $this->getPaginatedResult($brands, $this->params('page'));
        $view->navKey = self::NAV_KEY_BRAND;
        $view->messages = $this->flashMessenger()->getMessages();
        $view->errorMessages = $this->flashMessenger()->getErrorMessages();
        $view->form = $form;
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