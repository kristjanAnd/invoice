<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 30.04.15
 * Time: 16:46
 */

namespace Application\Form;


use Application\Entity\Subject\Company;
use Application\Entity\User;
use Application\Service\ArticleService;
use Application\Service\LanguageService;
use Application\Service\UnitService;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilterForm extends Form implements ServiceLocatorAwareInterface{

    private $locator;

    public static $statusNames = array(
        'active',
        'disabled',
        'pending',
        'archived',
        'confirmed'
    );

    public static $dateNames = array(
        'fromDate',
        'toDate'
    );

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->locator = $serviceLocator;
    }

    public function getServiceLocator() {

        return $this->locator;
    }

    /**
     * @var Translator
     */
    protected $translator;

    protected $dateFormats = array(
        'et' => 'd.m.Y',
        'ru' => 'd.m.Y',
        'us' => 'd/m/Y'

    );

    protected $filterDateFormat = 'd.m.Y';

    /**
     * @return string
     */
    public function getFilterDateFormat()
    {
        return $this->filterDateFormat;
    }

    /**
     * @param string $languageCode
     */
    public function setFilterDateFormat($languageCode = null)
    {
        if(!$languageCode){
            $languageCode = $this->locator->get('Config')['languages']['defaultIdentificator'];
        }
        $this->filterDateFormat = $this->dateFormats[$languageCode];
        return $this;
    }

    /**
     * @var Company
     */
    protected $company;
    /**
     * @var UnitService
     */
    protected $unitService;
    /**
     * @var ArticleService
     */
    protected $articleService;

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
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     *
     * @param Translator $t
     */
    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

    public function setDefaultFilterDates(){
        $toDate = new \DateTime();
        $fromDate = clone $toDate;
        $fromDate->modify('-1 month');
        $this->get('fromDate')->setValue($fromDate->format($this->getFilterDateFormat()));
        $this->get('toDate')->setValue($toDate->format($this->getFilterDateFormat()));
    }

    public function getUnitSelect(){
        $result = array();
        if(!$this->company){
            return $result;
        }
        foreach($this->unitService->getActiveCompanyUnits($this->company) as $unit){
            $result[$unit->getId()] = $unit->getCode();
        }
        return $result;
    }

    public function getCompanyUsersSelect(){
        $result = array();
        if($this->company){
            foreach($this->company->getUsers() as $user){
                $username = $user->getFullName();
                if($user->getStatus() !== User::STATUS_ACTIVE){
                    $username = $user->getFullName() . ' (' . $this->translator->translate('Filter.form.clintUser.notActiveNotice') . ')';
                }
                $result[$user->getId()] = $username;
            }
        }
        return $result;
    }

    public function getArticleBrandSelect(){
        $result = array();
        if(!$this->company){
            return $result;
        }
        foreach($this->articleService->getActiveCompanyArticleBrands($this->company) as $brand){
            $result[$brand->getId()] = $brand->getName();
        }
        return $result;
    }

    public function getArticleCategorySelect(){
        $result = array();
        if(!$this->company){
            return $result;
        }
        foreach($this->articleService->getActiveCompanyArticleCategories($this->company) as $category){
            $result[$category->getId()] = $category->getName();
        }
        return $result;
    }

    public function init()
    {
        $active = new Checkbox('active');
        $this->add($active);

        $disabled = new Checkbox('disabled');
        $this->add($disabled);

        $pending = new Checkbox('pending');
        $pending->setAttribute('checked', 'checked');
        $pending->setValue(1);
        $this->add($pending);

        $archived = new Checkbox('archived');
        $this->add($archived);

        $confirmed = new Checkbox('confirmed');
        $this->add($confirmed);

        $name = new Text('name');
        $name->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.name.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($name);

        $clientName = new Text('clientName');
        $clientName->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.clientName.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($clientName);

        $id = new Text('entityId');
        $id->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.entityId.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($id);

        $number = new Text('entityNumber');
        $number->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.entityNumber.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($number);

        $fromDate = new Text('fromDate');
        $fromDate->setAttributes(array(
            'id' => 'fromDate',
            'placeholder' => $this->translator->translate('Filter.form.fromDate.placeholder'),
            'class' => 'form-control',
            'readonly' => 'readonly'
        ));
        $this->add($fromDate);

        $toDate = new Text('toDate');
        $toDate->setAttributes(array(
            'id' => 'toDate',
            'placeholder' => $this->translator->translate('Filter.form.toDate.placeholder'),
            'class' => 'form-control',
            'readonly' => 'readonly'
        ));
        $this->add($toDate);

        $email = new Text('email');
        $email->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.email.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($email);

        $address = new Text('address');
        $address->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.address.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($address);

        $regNo = new Text('regNo');
        $regNo->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.regNo.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($regNo);

        $vatNo = new Text('vatNo');
        $vatNo->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.vatNo.placeholder'),
            'class' => 'form-control'
        ));
        $this->add($vatNo);

        $code = new Text('code');
        $code->setAttributes(array(
            'placeholder' => $this->translator->translate('Filter.form.code.placeholder'),
            'class' => 'form-control',
        ));
        $this->add($code);

        $clientUser = new Select('clientUser');
        $clientUser->setAttributes(array(
            'class' => 'form-control'
        ));
        $clientUser->setEmptyOption($this->translator->translate('FilterForm.clientUser.emptyOption'));
        $clientUser->setValueOptions($this->getCompanyUsersSelect());
        $this->add($clientUser);

        $invoiceUser = new Select('invoiceUser');
        $invoiceUser->setAttributes(array(
            'class' => 'form-control'
        ));
        $invoiceUser->setEmptyOption($this->translator->translate('FilterForm.invoiceUser.emptyOption'));
        $invoiceUser->setValueOptions($this->getCompanyUsersSelect());
        $this->add($invoiceUser);

        $articleCategory = new Select('articleCategory');
        $articleCategory->setAttributes(array(
            'class' => 'form-control'
        ));
        $articleCategory->setEmptyOption($this->translator->translate('FilterForm.articleCategory.emptyOption'));
        $articleCategory->setValueOptions($this->getArticleCategorySelect());
        $this->add($articleCategory);

        $articleBrand = new Select('articleBrand');
        $articleBrand->setAttributes(array(
            'class' => 'form-control'
        ));
        $articleBrand->setEmptyOption($this->translator->translate('FilterForm.articleBrand.emptyOption'));
        $articleBrand->setValueOptions($this->getArticleBrandSelect());
        $this->add($articleBrand);

        return $this;
    }

    public function getInputFilter()
    {
        if ($this->filter == null) {
            $this->filter = new InputFilter();

            $clientName = new Input('clientName');
            $clientName->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($clientName);

            $active = new Input('active');
            $active->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($active);

            $disabled = new Input('disabled');
            $disabled->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($disabled);

            $pending = new Input('pending');
            $pending->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($pending);

            $archived = new Input('archived');
            $archived->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($archived);

            $confirmed = new Input('confirmed');
            $confirmed->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($confirmed);

            $entityId = new Input('entityId');
            $entityId->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($entityId);

            $entityNumber = new Input('entityNumber');
            $entityNumber->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($entityNumber);

            $fromDate = new Input('fromDate');
            $fromDate->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($fromDate);

            $toDate = new Input('toDate');
            $toDate->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($toDate);

            $articleCategory = new Input('articleCategory');
            $articleCategory->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($articleCategory);

            $articleBrand = new Input('articleBrand');
            $articleBrand->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($articleBrand);

            $clientUser = new Input('clientUser');
            $clientUser->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($clientUser);

            $invoiceUser = new Input('invoiceUser');
            $invoiceUser->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($invoiceUser);

            $email = new Input('email');
            $email->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($email);

            $address = new Input('address');
            $address->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($address);

            $regNo = new Input('regNo');
            $regNo->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($regNo);

            $vatNo = new Input('vatNo');
            $vatNo->setRequired(false)->setAllowEmpty(true);
            $this->filter->add($vatNo);
        }

        return $this->filter;
    }


} 