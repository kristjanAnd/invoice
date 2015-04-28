<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 8:35
 */

namespace Application\Service;


use Application\Entity\Article\Brand;
use Application\Entity\Article\Category;
use Application\Entity\Subject\Company;
use Zend\Stdlib\Parameters;

class ArticleService extends AbstractService {

    public function getBrandStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Brand::STATUS_ACTIVE => $translator->translate('Brand.status.active'),
            Brand::STATUS_DISABLED => $translator->translate('Brand.status.disabled')
        );
    }

    public function getCategoryStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Brand::STATUS_ACTIVE => $translator->translate('Brand.status.active'),
            Brand::STATUS_DISABLED => $translator->translate('Brand.status.disabled')
        );
    }

    public function getCategoriesByCompany(Company $company, $status = null){
        $data = array('company' => $company);
        if($status){
            $data['status'] = $status;
        }
        return $this->entityManager->getRepository(Category::getClass())->findBy($data);
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

    public function getBrandsByCompany(Company $company, $status = null){
        $data = array('company' => $company);
        if($status){
            $data['status'] = $status;
        }
        return $this->entityManager->getRepository(Brand::getClass())->findBy($data);
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
} 