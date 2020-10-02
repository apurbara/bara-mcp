<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Program;

class ProgramView
{
    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;
    
    function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }
    
    public function showById(string $firmId, string $programId): Program
    {
        return $this->programRepository->ofId($firmId, $programId);
    }
    
    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return Program[]
     */
    public function showAll(string $firmId, int $page, int $pageSize)
    {
        return $this->programRepository->allProgramViewableByClient($firmId, $page, $pageSize);
    }

}
