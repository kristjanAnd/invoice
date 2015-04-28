<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 15:29
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ErrorMessages extends AbstractHelper {

    public function __invoke(array $messages = null) {
        $messageAlert = '';
        if ($messages != null && is_array($messages) && count($messages) > 0) {
            $messageAlert = $this->createMessages($messages, $messageAlert);
        }

        return $messageAlert;
    }

    public function createMessages(array $messages, $messageAlert) {
        foreach($messages as $message){ /* @var $message \*/
            $html = <<<EOL
<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{$message}</div>
EOL;
            $messageAlert .= $html;
        }

        return $messageAlert;
    }
} 