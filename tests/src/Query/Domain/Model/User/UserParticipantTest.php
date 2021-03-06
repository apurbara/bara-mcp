<?php

namespace Query\Domain\Model\User;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\LearningMaterialFinder;
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $participant;
    protected $learningMaterialFinder;
    protected $learningMaterialId = "learningMaterialId";
    protected $page = 1, $pageSize = 25;
    protected $missionCommentRepository, $missionId = 'missionId', $missionCommentId = 'missionCommentId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = new TestableUserParticipant();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant->participant = $this->participant;
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
    }
    
    public function test_viewLearningMaterial_returnParticipantViewLearningMaterialResult()
    {
        $this->participant->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->userParticipant->viewLearningMaterial($this->learningMaterialFinder, $this->learningMaterialId);
    }
    
    public function test_pullRecordedEvents_returnParticipantsPullRecordedEventsResult()
    {
        $this->participant->expects($this->once())
                ->method("pullRecordedEvents");
        $this->userParticipant->pullRecordedEvents();
    }
    
    public function test_viewMissionComment_returnParticipantsViewMissionCommentResult()
    {
        $this->participant->expects($this->once())
                ->method('viewMissionComment')
                ->with($this->missionCommentRepository, $this->missionCommentId);
        $this->userParticipant->viewMissionComment($this->missionCommentRepository, $this->missionCommentId);
    }
    public function test_viewAllMissionComments_returnParticpantsViewAllMissionCommentsResult()
    {
        $this->participant->expects($this->once())
                ->method('viewAllMissionComments')
                ->with($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
        $this->userParticipant->viewAllMissionComments($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
    }
}

class TestableUserParticipant extends UserParticipant
{
    public $user;
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
