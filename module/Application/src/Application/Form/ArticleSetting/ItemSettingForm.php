<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 7.05.15
 * Time: 15:26
 */

namespace Application\Form\ArticleSetting;


use Application\Entity\ArticleSetting\ItemSetting;
use Application\Form\ArticleSettingForm;

class ItemSettingForm extends ArticleSettingForm {

    public function init(){
        parent::init();
        $this->get('vat')->setAttributes(array('id' => 'item-vat'));
        $this->get('unit')->setAttributes(array('id' => 'item-unit'));
        $this->get('quantity')->setAttributes(array('id' => 'item-quantity'));
        return $this;
    }

    public function getInputFilter(){
        $this->filter = parent::getInputFilter();
        return $this->filter;
    }

    public function setFormValues(ItemSetting $itemSetting){
        parent::setFormValues($itemSetting);
    }

    public function disableFields(){
        parent::disableFields();
    }
} 