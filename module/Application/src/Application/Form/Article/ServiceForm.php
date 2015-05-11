<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 8.05.15
 * Time: 7:49
 */

namespace Application\Form\Article;


use Application\Entity\Article\Service;
use Application\Entity\ArticleSetting\ServiceSetting;
use Application\Form\ArticleForm;

class ServiceForm extends ArticleForm {

    public function init(){
        parent::init();
        return $this;
    }

    public function getInputFilter(){
        $this->filter = parent::getInputFilter();
        return $this->filter;
    }

    public function setFormValues(Service $service){
        parent::setFormValues($service);
    }

    public function setDefaults(ServiceSetting $serviceSetting = null){
        parent::setDefaults($serviceSetting);
    }
} 