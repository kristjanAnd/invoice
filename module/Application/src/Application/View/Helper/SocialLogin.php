<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 11.03.15
 * Time: 18:38
 */

namespace Application\View\Helper;

use Application\Common\SocialStorage;
use Zend\View\Helper\AbstractHelper;

class SocialLogin extends AbstractHelper
{
    public function __invoke($provider, $redirect = false)
    {
        $name = 'scn-social-auth-user/login/provider';
        $params = array('provider' => $provider);
        $options = array();

        if ($redirect) {
            $options = array(
                'query' => array(
                    'redirect' => $redirect,
                ),
            );
        }

        $url = $this->view->url($name, $params, $options);

        echo '<a class="btn" href="' . $url . '"><i class="' . SocialStorage::$providerFontClass[$provider] . '"></i>  ' . ucfirst($provider) . '</a>';
    }
}
