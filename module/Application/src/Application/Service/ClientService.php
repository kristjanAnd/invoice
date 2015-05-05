<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 4.05.15
 * Time: 11:18
 */

namespace Application\Service;


use Application\Entity\Role;
use Application\Entity\Subject\Client;
use Application\Entity\Subject\Company;
use Application\Entity\User;
use Zend\Stdlib\Parameters;

class ClientService extends AbstractService {

    public function getCompanyClients(Company $company, Parameters $data = null){
        return $this->entityManager->getRepository(Client::getClass())->getCompanyClients($company, $data);
    }

    public function getCompanyActiveClients(Company $company){
        return $this->entityManager->getRepository(Client::getClass())->findBy(array('company' => $company, 'status' => Client::STATUS_ACTIVE));
    }

    public function getCompanyActiveClientSelect(Company $company){
        $result = array();
        foreach($this->getCompanyActiveClients($company) as $client){
            $result[$client->getId()] = $client->getName();
        }
        return $result;
    }

    public function getClientStatusSelect(){
        $translator = $this->locator->get('Translator');
        return array(
            Client::STATUS_ACTIVE => $translator->translate('Client.status.active'),
            Client::STATUS_DISABLED => $translator->translate('Client.status.disabled')
        );
    }

    public function getFilterData(Parameters $data, User $user){
        $filterData = new Parameters();
        $filterData->statuses = array();
        if(!$user->isMaster() || $user->getRoles()->last()->getRoleId() !== Role::ROLE_ADMIN){
            $filterData->clientUser = $user;
        }

        if(isset($data->clientUser) && $data->clientUser > 0){
            $clientUser = $this->entityManager->getRepository(User::getClass())->findOneBy(array('id' => $data->clientUser));
            if($clientUser && $clientUser->getCompany() == $user->getCompany()){
                $filterData->clientUser = $clientUser;
            }
        }
        if(isset($data->active) && $data->active == 1){
            $filterData->statuses[] = Client::STATUS_ACTIVE;
        }
        if(isset($data->disabled) && $data->disabled == 1){
            $filterData->statuses[] = Client::STATUS_DISABLED;
        }
        if(isset($data->name) && strlen(trim($data->name)) > 0){
            $filterData->name = $data->name;
        }
        if(isset($data->code) && strlen(trim($data->code)) > 0){
            $filterData->code = $data->code;
        }
        if(isset($data->email) && strlen(trim($data->email)) > 0){
            $filterData->email = $data->email;
        }
        if(isset($data->address) && strlen(trim($data->address)) > 0){
            $filterData->address = $data->address;
        }
        if(isset($data->regNo) && strlen(trim($data->regNo)) > 0){
            $filterData->regNo = $data->regNo;
        }
        if(isset($data->vatNo) && strlen(trim($data->vatNo)) > 0){
            $filterData->vatNo = $data->vatNo;
        }
        return $filterData;
    }

    /**
     * @param $id
     * @return null|Client
     */
    public function getClientById($id){
        return $this->entityManager->getRepository(Client::getClass())->findOneBy(array('id' => $id));
    }

    public function saveClient(Client $client, Parameters $data){
        if(isset($data->name)){
            $client->setName($data->name);
        }
        if(isset($data->code)){
            $client->setCode($data->code);
        }
        if(isset($data->email)){
            $client->setEmail($data->email);
        }
        if(isset($data->regNo)){
            $client->setRegistrationNumber($data->regNo);
        }
        if(isset($data->phone)){
            $client->setPhone($data->phone);
        }
        if(isset($data->vatNo)){
            $client->setVatNumber($data->vatNo);
        }
        if(isset($data->address)){
            $client->setAddress($data->address);
        }
        if(isset($data->status)){
            $client->setStatus($data->status);
        }
        if(isset($data->delayPercent)){
            $client->setDelayPercent($data->delayPercent);
        }
        if(isset($data->deadlineDays)){
            $client->setDeadlineDays($data->deadlineDays);
        }
        if(isset($data->referenceNumber)){
            $client->setReferenceNumber($data->referenceNumber);
        }
        if(isset($data->clientUser)){
            $clientUser = $this->entityManager->getRepository(User::getClass())->findOneBy(array('id' => $data->clientUser));
            if($clientUser && $clientUser->getCompany() == $client->getCompany()){
                $client->setClientUser($clientUser);
            }
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush($client);

        return $client;
    }
}