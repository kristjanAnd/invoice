<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 17:44
 */

namespace Application\Service;


use Application\Entity\Document;

class DocumentService extends AbstractService {

    public function getDateFormatSelect(){
        $date = new \DateTime();
        $result = array();
        foreach(Document::$dateFormats as $format => $v){
            $result[$format] = $format . ' => ' . $v . $date->format('Y');
        }
        return $result;
    }
}