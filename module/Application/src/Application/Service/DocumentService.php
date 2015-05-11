<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 17:44
 */

namespace Application\Service;


use Application\Entity\Article;
use Application\Entity\Document;
use Application\Entity\DocumentRow;
use Application\Entity\User;
use Application\Entity\Vat;
use Zend\Stdlib\Parameters;

class DocumentService extends AbstractService {

    public function getDateFormatSelect(){
        $date = new \DateTime();
        $result = array();
        foreach(Document::$dateFormats as $format => $v){
            $result[$format] = $format . ' => ' . $v . $date->format('Y');
        }
        return $result;
    }

    public function saveDocument(Document $document, Parameters $data, $dateFormat){
        if(isset($data->documentDate)) {
            $documentDate = \DateTime::createFromFormat($dateFormat, $data->documentDate);
            if ($documentDate) {
                $document->setDocumentDate($documentDate);
            }
        }
        if(isset($data->subjectName)){
            $document->setSubjectName($data->subjectName);
        }
        if(isset($data->subjectEmail)){
            $document->setSubjectEmail($data->subjectEmail);
        }
        if(isset($data->subjectRegNo)){
            $document->setSubjectRegNo($data->subjectRegNo);
        }
        if(isset($data->subjectVatNo)){
            $document->setSubjectVatNo($data->subjectVatNo);
        }
        if(isset($data->amount)){
            $document->setAmount($data->amount);
        }
        if(isset($data->vatAmount)){
            $document->setVatAmount($data->vatAmount);
        }
        if(isset($data->amountVat)){
            $document->setAmountVat($data->amountVat);
        }
        if(isset($data->user)){
            $user = $this->entityManager->getRepository(User::getClass())->findOneBy(array('id' => $data->user));
            if($user && $user->getCompany() == $document->getCompany()){
                $document->setUser($user);
            }
        }
        if(isset($data->language)){
            $document->setLanguageCode($data->language);
        }
        if(isset($data->dateFormat)){
            $document->setDateFormat($data->dateFormat);
        }

        $this->entityManager->persist($document);
        $this->entityManager->flush($document);

        return $document;

    }

    public function saveDocumentRow(DocumentRow $row, Parameters $data){
        if(isset($data->amount)){
            $row->setAmount($data->amount);
        }
        if(isset($data->vatAmount)){
            $row->setVatAmount($data->vatAmount);
        }
        if(isset($data->amountVat)){
            $row->setAmountVat($data->amountVat);
        }
        if(isset($data->article)){
            $article = $this->entityManager->getRepository(Article::getClass())->findOneBy(array('id' => $data->user));
            if($article){
                $row->setArticle($article);
            }
        }
        if(isset($data->name)){
            $row->setName($data->name);
        }
        if(isset($data->quantity)){
            $row->setQuantity($data->quantity);
        }
        if(isset($data->name)){
            $row->setName($data->name);
        }
    }
}