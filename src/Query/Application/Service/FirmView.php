<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm;

class FirmView
{
    /**
     *
     * @var FirmRepository
     */
    protected $firmRepository;
    
    function __construct(FirmRepository $firmRepository)
    {
        $this->firmRepository = $firmRepository;
    }
    
    public function showById(string $firmId): Firm
    {
        return $this->firmRepository->ofId($firmId);
    }
    
    /**
     * 
     * @param int $page
     * @param int $pageSize
     * @return Firm[]
     */
    public function showAll(int $page, int $pageSize)
    {
        return $this->firmRepository->all($page, $pageSize);
    }
    
    public function showByIdentifier(string $firmIdentifier): Firm
    {
        return $this->firmRepository->ofIdentifier($firmIdentifier);
    }

}
