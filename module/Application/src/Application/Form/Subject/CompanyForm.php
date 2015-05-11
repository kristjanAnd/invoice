<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 11.05.15
 * Time: 15:43
 */

namespace Application\Form\Subject;


use Application\Entity\Subject\Company;
use Application\Form\SubjectForm;

class CompanyForm extends SubjectForm {

    public function init(){
        parent::init();
        return $this;
    }

    public function getInputFilter(){
        $filter = parent::getInputFilter();
        return $filter;
    }

    public function setFormValues(Company $company){
        parent::setFormValues($company);
    }
} 