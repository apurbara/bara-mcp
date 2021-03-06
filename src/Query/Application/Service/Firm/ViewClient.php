<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Client;

class ViewClient
{
    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;
    
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return Client[]
     */
    public function showAll(string $firmId, int $page, int $pageSize, ?bool $activatedStatus)
    {
        return $this->clientRepository->all($firmId, $page, $pageSize, $activatedStatus);
    }
    
    public function showById(string $firmId, string $clientId): Client
    {
        return $this->clientRepository->ofId($firmId, $clientId);
    }
    
    public function showByEmail(string $firmId, string $email): Client
    {
        return $this->clientRepository->aClientByEmail($firmId, $email);
    }

}
