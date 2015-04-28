<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 23.04.15
 * Time: 18:25
 */

namespace Application\Service;


class LanguageService extends AbstractService {

    public function getLanguageSelect(){
        $result = array();
        $languages = $this->locator->get('Config')['languages']['available'];
        foreach($languages as $k => $v){
            $result[$k] = $this->getTranslatedLanguageNameByIdentificator($k);
        }

        return $result;
    }

    public function getTranslatedLanguageNameByIdentificator($id){
        $translator = $this->locator->get('Translator');

        $translations =  array(
            'et' => $translator->translate('language.select.et'),
            'us' => $translator->translate('language.select.us'),
            'ru' => $translator->translate('language.select.ru'),
        );

        return isset($translations[$id]) ? $translations[$id] : $translations['et'];
    }
} 