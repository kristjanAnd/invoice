<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 9/18/14
 * Time: 11:37 AM
 */

namespace Application\Controller\Plugin;

use Application\Common\Message\ErrorMessage;

use Application\View\Helper\Messages;

use Application\Common\Message;

class FlashMessenger extends \Zend\Mvc\Controller\Plugin\FlashMessenger {

    const DEFAULT_MESSAGE_TYPE_ERROR = 'error';
    const DEFAULT_MESSAGE_TYPE_INFO = 'info';
    const DEFAULT_MESSAGE_TYPE_SUCCESS = 'success';
    const DEFAULT_MESSAGE_TYPE_WARNING = 'warning';

    public function addError($title, $description = null, array $messages = null) {

        return $this->addSpecificMessage(self::DEFAULT_MESSAGE_TYPE_ERROR, $title, $description, $messages);
    }

    public function addInfo($title, $description = null, array $messages = null) {

        return $this->addSpecificMessage(self::DEFAULT_MESSAGE_TYPE_INFO, $title, $description, $messages);
    }

    public function addSuccess($title, $description = null, array $messages = null) {

        return $this->addSpecificMessage(self::DEFAULT_MESSAGE_TYPE_SUCCESS, $title, $description, $messages);
    }

    public function addWarning($title, $description = null, array $messages = null) {

        return $this->addSpecificMessage(self::DEFAULT_MESSAGE_TYPE_WARNING, $title, $description, $messages);
    }

    public function addMessage($message) {
        return $this->addSpecificMessage(self::DEFAULT_MESSAGE_TYPE_SUCCESS, $message);
    }

    public function addMessages($type, array $messages) {
        foreach ($messages as $message) {
            $this->addSpecificMessage(self::DEFAULT_MESSAGE_TYPE_SUCCESS, $message);
        }
    }

    protected function addSpecificMessage($type, $title, $description = null, array $messages = null) {
        $className = '\Application\Common\Message\\' . ucfirst($type) . 'Message';
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class "' . $className . '" is undefined.');
        }

        $message = new $className($title, $description, $messages);

        return parent::addMessage($message);
    }

}