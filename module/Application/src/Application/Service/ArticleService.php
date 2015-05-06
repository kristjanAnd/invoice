<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 8:35
 */

namespace Application\Service;


use Application\Entity\Article;
use Application\Entity\Article\Brand;
use Application\Entity\Article\Category;
use Application\Entity\Article\Item;
use Application\Entity\Subject\Company;
use Application\Entity\Unit;
use Zend\Stdlib\Parameters;

class ArticleService extends AbstractService {

    public function getCategoriesByCompany(Company $company, Parameters $data){
        return $this->entityManager->getRepository(Category::getClass())->getCompanyArticleCategories($company, $data);
    }

    public function getFilterData(Parameters $data){
        $filterData = new Parameters();
        $filterData->statuses = array();
        if(isset($data->active) && $data->active == 1){
            $filterData->statuses[] = Unit::STATUS_ACTIVE;
        }
        if(isset($data->disabled) && $data->disabled == 1){
            $filterData->statuses[] = Unit::STATUS_DISABLED;
        }
        if(isset($data->name) && strlen(trim($data->name)) > 0){
            $filterData->name = $data->name;
        }
        if(isset($data->code) && strlen(trim($data->code)) > 0){
            $filterData->code = $data->code;
        }
        if(isset($data->articleCategory) && $data->articleCategory > 0){
            $category = $this->getCategoryById($data->articleCategory);
            if($category){
                $filterData->category = $category;
            }
        }
        if(isset($data->articleBrand) && $data->articleBrand > 0){
            $brand = $this->getBrandById($data->articleBrand);
            if($brand){
                $filterData->brand = $brand;
            }
        }
        return $filterData;
    }

    public function getItemsByCompany(Company $company, Parameters $data){
        return $this->entityManager->getRepository(Item::getClass())->getCompanyItems($company, $data);
    }

    public function getServicesByCompany(Company $company, Parameters $data){
        return $this->entityManager->getRepository(Article\Service::getClass())->getCompanyServices($company, $data);
    }

    public function getActiveCompanyArticleCategories(Company $company){
        return $this->entityManager->getRepository(Category::getClass())->findBy(array('company' => $company, 'status' => Category::STATUS_ACTIVE));
    }

    public function getActiveCompanyArticleBrands(Company $company){
        return $this->entityManager->getRepository(Brand::getClass())->findBy(array('company' => $company, 'status' => Brand::STATUS_ACTIVE));
    }

    public function getActiveCompanyItems(Company $company){
        return $this->entityManager->getRepository(Item::getClass())->findBy(array('company' => $company, 'status' => Item::STATUS_ACTIVE));
    }

    public function getActiveCompanyServices(Company $company){
        return $this->entityManager->getRepository(Article\Service::getClass())->findBy(array('company' => $company, 'status' => Article\Service::STATUS_ACTIVE));
    }

    public function getItemSelect(Company $company){
        $result = array();
        foreach($this->getActiveCompanyItems($company) as $item){
            $result[$item->getId()] = $item->getName() . ' ' . $item->getCode();
        }
        return $result;
    }

    public function getServiceSelect(Company $company){
        $result = array();
        foreach($this->getActiveCompanyServices($company) as $service){
            $result[$service->getId()] = $service->getName() . ' ' . $service->getCode();
        }
        return $result;
    }

    public function getArticleTypeSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Article::ARTICLE_TYPE_ITEM => $translator->translate('Article.type.item'),
            Article::ARTICLE_TYPE_SERVICE => $translator->translate('Article.type.service')
        );
    }

    /**
     * @param $id
     * @return null|Category
     */
    public function getCategoryById($id){
        return $this->entityManager->getRepository(Category::getClass())->findOneBy(array('id' => $id));
    }

    /**
     * @param $id
     * @return null|Brand
     */
    public function getBrandById($id){
        return $this->entityManager->getRepository(Brand::getClass())->findOneBy(array('id' => $id));
    }

    public function getBrandsByCompany(Company $company, Parameters $data = null){
        return $this->entityManager->getRepository(Brand::getClass())->getCompanyArticleBrands($company, $data);
    }

    /**
     * @param $id
     * @return null|Article
     */
    public function getArticleById($id){
        return $this->entityManager->getRepository(Article::getClass())->findOneBy(array('id' => $id));
    }

    public function saveArticle(Article $article, Parameters $data){
        if(isset($data->name)){
            $article->setName($data->name);
        }
        if(isset($data->code)){
            $article->setCode($data->code);
        }
        if(isset($data->quantity)){
            $article->setQuantity($data->quantity);
        }
        if(isset($data->salePrice)){
            $article->setSalePrice($data->salePrice);
        }
        if(isset($data->description)){
            $article->setDescription($data->description);
        }
        if(isset($data->status)){
            $article->setStatus($data->status);
        }

        if(isset($data->unit)){
            $unit = $this->entityManager->getRepository(Unit::getClass())->findOneBy(array('id' => $data->unit));
            if($unit){
                $article->setUnit($unit);
            }
        }
        if(isset($data->category)){
            $category = $this->getCategoryById($data->category);
            if($category){
                $article->setCategory($category);
            }
        }
        if(isset($data->brand)){
            $brand = $this->getBrandById($data->brand);
            if($brand){
                $article->setBrand($brand);
            }
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush($article);

        return $article;
    }

    public function saveCategory(Category $category, Parameters $data){
        if(isset($data->code)){
            $category->setCode($data->code);
        }
        if(isset($data->name)){
            $category->setName($data->name);
        }
        if(isset($data->description)){
            $category->setDescription($data->description);
        }
        if(isset($data->status)){
            $category->setStatus($data->status);
        }
        $this->entityManager->persist($category);
        $this->entityManager->flush($category);

        return $category;
    }

    public function saveBrand(Brand $brand, Parameters $data){
        if(isset($data->name)){
            $brand->setName($data->name);
        }
        if(isset($data->status)){
            $brand->setStatus($data->status);
        }
        $this->entityManager->persist($brand);
        $this->entityManager->flush($brand);

        return $brand;
    }

    public function getUnitStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Unit::STATUS_ACTIVE => $translator->translate('Unit.status.active'),
            Unit::STATUS_DISABLED => $translator->translate('Unit.status.disabled')
        );
    }

    public function getArticleStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Article::STATUS_ACTIVE => $translator->translate('Article.status.active'),
            Article::STATUS_DISABLED => $translator->translate('Article.status.disabled')
        );
    }

    public function getArticleCategoryStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Category::STATUS_ACTIVE => $translator->translate('ArticleCategory.status.active'),
            Category::STATUS_DISABLED => $translator->translate('ArticleCategory.status.disabled')
        );
    }

    public function getArticleBrandStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Brand::STATUS_ACTIVE => $translator->translate('ArticleBrand.status.active'),
            Brand::STATUS_DISABLED => $translator->translate('ArticleBrand.status.disabled')
        );
    }
} 