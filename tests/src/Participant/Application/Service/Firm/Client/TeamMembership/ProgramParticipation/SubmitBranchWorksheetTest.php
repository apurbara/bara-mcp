<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\{
    Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Firm\Program\MissionRepository,
    Application\Service\Participant\WorksheetRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\Participant\Worksheet,
    Domain\Model\TeamProgramParticipation
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitBranchWorksheetTest extends TestBase
{

    protected $service;
    protected $worksheetRepository, $parentWorksheet, $nextId = 'nextId';
    protected $teamMembershipRepository, $teamMembership;
    protected $missionRepository, $mission;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $parentWorksheetId = "parentWorksheetId", $missionId = "missionId";
    protected $name = "worksheet name", $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())->method("nextIdentity")->willReturn($this->nextId);
        $this->parentWorksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository->expects($this->any())
                ->method("ofId")
                ->with($this->parentWorksheetId)
                ->willReturn($this->parentWorksheet);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);

        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method("ofId")
                ->with($this->missionId)
                ->willReturn($this->mission);

        $this->service = new SubmitBranchWorksheet(
                $this->worksheetRepository, $this->teamMembershipRepository, $this->missionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamMembershipId, $this->parentWorksheetId,
                        $this->missionId, $this->name, $this->formRecordData);
    }

    public function test_execute_addBranchWorksheetToRepository()
    {
        $branchWorksheet = $this->buildMockOfClass(Worksheet::class);
        $this->teamMembership->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->parentWorksheet, $this->nextId, $this->name,  $this->mission, $this->formRecordData)
                ->willReturn($branchWorksheet);
        $this->worksheetRepository->expects($this->once())
                ->method("add")
                ->with($branchWorksheet);
        $this->execute();
    }

    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
