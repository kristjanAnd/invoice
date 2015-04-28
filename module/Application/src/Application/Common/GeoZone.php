<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 13.04.15
 * Time: 14:52
 */

namespace Application\Common;


class GeoZone {

    const COUNTRY_CODE_ESTONIA = 'EE';
    const COUNTRY_CODE_RUSSIA = 'RU';

    const LANGUAGE_CODE_ET = 'et';
    const LANGUAGE_CODE_RU = 'ru';
    const LANGUAGE_CODE_US = 'us';

    public static $countryLocaleMapper = array(
        self::COUNTRY_CODE_ESTONIA => self::LANGUAGE_CODE_ET,
        self::COUNTRY_CODE_RUSSIA => self::LANGUAGE_CODE_RU
    );

    public static function getIpAddress(){
        $ip = getenv('HTTP_CLIENT_IP')?:
            getenv('HTTP_X_FORWARDED_FOR')?:
                getenv('HTTP_X_FORWARDED')?:
                    getenv('HTTP_FORWARDED_FOR')?:
                        getenv('HTTP_FORWARDED')?:
                            getenv('REMOTE_ADDR');

        return $ip;
    }

    public static function getGeoLocationInfoArray(){
        return unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . self::getIpAddress()));
    }
} 