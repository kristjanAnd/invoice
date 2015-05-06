<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 23.04.15
 * Time: 18:25
 */

namespace Application\Service;


class LanguageService extends AbstractService {

    public function getLanguageSelect() {
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

    public static function getCurrentLanguageCode($locator){
        /* @var $sessionManager \Zend\Session\SessionManager */
        $sessionManager = $locator->get('Zend\Session\SessionManager');
        $availableLanguages = $locator->get('Config')['languages']['available'];
        $sessionStorage = $sessionManager->getStorage();
        $code = $sessionStorage->offsetGet('language');
        if(!array_key_exists($code, $availableLanguages)){
            $code = $locator->get('Config')['languages']['defaultIdentificator'];
        }
        return $code;
    }

    public static function getCurrentLocale($locator){
        /* @var $sessionManager \Zend\Session\SessionManager */
        $sessionManager = $locator->get('Zend\Session\SessionManager');
        $availableLanguages = $locator->get('Config')['languages']['available'];
        $sessionStorage = $sessionManager->getStorage();
        $locale = $sessionStorage->offsetGet('locale');
        if(!in_array($locale, $availableLanguages)){
            $locale = $locator->get('Config')['languages']['defaultLocale'];
        }
        return $locale;
    }

    public static function getDateFormatByLanguageCode($code){
        $dateFormats = array(
            'et' => 'd.m.Y',
            'ru' => 'd.m.Y',
            'us' => 'd/m/Y'

        );
        if(array_key_exists($code, $dateFormats)){
           $format =  $dateFormats[$code];
        } else {
            $format = 'd.m.Y';
        }
        return $format;
    }
} 