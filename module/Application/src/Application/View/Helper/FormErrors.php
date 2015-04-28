<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 24.04.15
 * Time: 7:26
 */

namespace Application\View\Helper;


use Zend\Form\Form;
use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\I18n\Translator;

class FormErrors extends AbstractHelper {
    /**
     * @var Translator
     */
    protected $translator;

    public function setTranslator(Translator $t)
    {
        $this->translator = $t;
    }

    public function __invoke(Form $form = null) {
        if ($form && $this->hasFormErrors($form)) {
            return $this->render($form);
        }
        return null;
    }

    protected function hasFormErrors(Form $form) {
        foreach ($form->getMessages() as $message) {
            if (count($message) > 0) {
                return true;
            }
        }

        return false;
    }

    protected function render(Form $form){
        $output = "<ul>";
        foreach($form->getMessages() as $elementName => $element){
            $subElementNames = explode('-', $elementName);
            $formElement = $form->get($subElementNames[0]);
            for($i = 1; $i < count($subElementNames); $i++){
                $formElement = $formElement->get($subElementNames[$i]);
            }
            foreach ($element as $message){
                if ($formElement->getLabel() != null){
                    $subOutput = "<li>". $this->translator->translate($message) . '</li>';
//                    $subOutput .= $formElement->getAttribute('placeholder') != null ? $this->translator->translate($formElement->getAttribute('placeholder')) : $this->translator->translate($formElement->getLabel());
//                    $subOutput .= ': ' . $this->translator->translate($message) . '</li>';
                    $output .= $subOutput;
                }
            }
        }
        $output .= "</ul>";

        $html = <<<EOL
<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{$output}</div>
EOL;

        return $html;
    }
} 