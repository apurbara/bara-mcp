<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Tests\src\Participant\Application\Service\Client\AsTeamMember\ObjectiveProgressReportBaseTest;

class CancelObjectiveProgressReportSubmissionTest extends ObjectiveProgressReportBaseTest
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CancelObjectiveProgressReportSubmission(
                $this->teamMemberRepository, $this->teamParticipantRepository, $this->objectiveProgressReportRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->objectiveProgressReportId);
    }
    public function test_execute_teamMemberCancelObjectiveProgressReportSubmission()
    {
        $this->teamMember->expects($this->once())
                ->method('cancelObjectiveProgressReportSubmission')
                ->with($this->teamParticipant, $this->objectiveProgressReport);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
