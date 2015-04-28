<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 21.04.15
 * Time: 17:13
 */

namespace Application\Service;


use Application\Entity\Subject\Company;
use Application\Entity\Unit;
use Zend\Stdlib\Parameters;

class UnitService extends AbstractService {

    public function getUnitsByCompany(Company $company){
        return $this->entityManager->getRepository(Unit::getClass())->findBy(array('company' => $company));
    }

    /**
     * @param $id
     * @return null|Unit
     */
    public function getUnitById($id){
        return $this->entityManager->getRepository(Unit::getClass())->findOneBy(array('id' => $id));
    }

    public function saveUnit(Unit $unit, Parameters $data){
        if(isset($data->code)){
            $unit->setCode($data->code);
        }
        if(isset($data->value)){
            $unit->setValue($data->value);
        }
        if(isset($data->status)){
            $unit->setStatus($data->status);
        }
        $this->entityManager->persist($unit);
        $this->entityManager->flush($unit);

        return $unit;
    }

    public function getUnitStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Unit::STATUS_ACTIVE => $translator->translate('Unit.status.active'),
            Unit::STATUS_DISABLED => $translator->translate('Unit.status.disabled')
        );
    }
} 