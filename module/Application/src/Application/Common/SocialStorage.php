<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 11.03.15
 * Time: 18:22
 */

namespace Application\Common;


class SocialStorage {
    public static $providers = array(
        'facebook',
        'foursquare',
        'github',
        'google',
        'linkedIn',
        'twitter',
        'yahoo',
        'tumblr',
        'mailru',
        'odnoklassniki',
        'vkontakte',
        'yandex',
        'instagram',
    );

    public static $providerFontClass = array(
        'facebook' => 'fa fa-facebook-square',
        'foursquare' => 'fa fa-foursquare',
        'github' => 'fa fa-github-square',
        'google' => 'fa fa-google-plus-square',
        'linkedIn' => 'fa fa-linkedin-square',
        'twitter' => 'fa fa-twitter-square',
        'yahoo' => 'fa fa-yahoo',
        'tumblr' => 'fa fa-tumblr-square',
        'mailru' => '',
        'odnoklassniki' => '',
        'vkontakte' => '',
        'yandex' => '',
        'instagram' => 'fa fa-instagram',
    );

    public static function resetSocialStorage(){
        foreach(self::$providers as $provider){
            if(isset($_SESSION["HA::STORE"]["hauth_session." . $provider . ".is_logged_in"])){
                unset($_SESSION["HA::STORE"]["hauth_session." . $provider . ".is_logged_in"]);
            }
        }
    }
} 