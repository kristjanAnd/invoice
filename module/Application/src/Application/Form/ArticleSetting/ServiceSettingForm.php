<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 7.05.15
 * Time: 15:27
 */

namespace Application\Form\ArticleSetting;


use Application\Entity\ArticleSetting\ServiceSetting;
use Application\Form\ArticleSettingForm;

class ServiceSettingForm extends ArticleSettingForm {

    public function init(){
        parent::init();
        $this->get('vat')->setAttributes(array('id' => 'service-vat'));
        $this->get('unit')->setAttributes(array('id' => 'service-unit'));
        $this->get('quantity')->setAttributes(array('id' => 'service-quantity'));
        return $this;
    }

    public function getInputFilter(){
        $this->filter = parent::getInputFilter();
        return $this->filter;
    }

    public function setFormValues(ServiceSetting $serviceSetting){
        parent::setFormValues($serviceSetting);
    }

    public function disableFields(){
        parent::disableFields();
    }
} 