<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 8.05.15
 * Time: 7:48
 */

namespace Application\Form\Article;


use Application\Entity\Article\Item;
use Application\Entity\ArticleSetting\ItemSetting;
use Application\Form\ArticleForm;

class ItemForm extends ArticleForm {

    public function init(){
        parent::init();
        return $this;
    }

    public function getInputFilter(){
        $this->filter = parent::getInputFilter();
        return $this->filter;
    }

    public function setFormValues(Item $item){
        parent::setFormValues($item);
    }

    public function setDefaults(ItemSetting $itemSetting = null){
        parent::setDefaults($itemSetting);
    }
}