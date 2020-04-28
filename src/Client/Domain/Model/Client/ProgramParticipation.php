<?php

namespace Client\Domain\Model\Client;

use Client\Domain\ {
    Event\ConsultationRequestMutatedByParticipantEvent,
    Event\ConsultationSessionMutatedByParticipantEvent,
    Model\Client,
    Model\Client\ProgramParticipation\ConsultationRequest,
    Model\Client\ProgramParticipation\ConsultationSession,
    Model\Firm\Program,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\ConsultationSetup
};
use DateTimeImmutable;
use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Resources\ {
    Domain\Model\ModelContainEvents,
    Exception\RegularException,
    Uuid
};
use Shared\Domain\Model\ConsultationRequestStatusVO;

class ProgramParticipation extends ModelContainEvents
{

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Program
     */
    protected $program;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $acceptedTime;

    /**
     * 
     * @var bool
     */
    protected $active = true;

    /**
     * 
     * @var string
     */
    protected $note = null;

    /**
     * 
     * @var ArrayCollection
     */
    protected $consultationRequests;

    /**
     * 
     * @var ArrayCollection
     */
    protected $consultationSessions;

    function getClient(): Client
    {
        return $this->client;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getProgram(): Program
    {
        return $this->program;
    }

    function getAcceptedTimeString(): ?string
    {
        return $this->acceptedTime->format('Y-m-d H:i:s');
    }

    function isActive(): bool
    {
        return $this->active;
    }

    function getNote(): ?string
    {
        return $this->note;
    }

    protected function __construct()
    {
        
    }

    public function quit(): void
    {
        $this->assertActive();
        $this->active = false;
        $this->note = 'quit';
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = 'forbidden: this request only allowed on active program participation';
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function createConsultationRequest(
            ConsultationSetup $consultationSetup, Consultant $consultant, DateTimeImmutable $startTime): ConsultationRequest
    {
        $consultationRequestId = Uuid::generateUuid4();
        $consultationRequest = new ConsultationRequest($this, $consultationRequestId, $consultationSetup, $consultant, $startTime);

        $this->assertNoConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);
        
        $messageForPersonnel = "you've received consultation request from {$this->client->getName()}";
        $event = new ConsultationRequestMutatedByParticipantEvent($consultationRequestId, $messageForPersonnel);
        $this->recordEvent($event);

        return $consultationRequest;
    }

    public function reproposeConsultationRequest(
            string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);
        $consultationRequest->rePropose($startTime);

        $this->assertNoConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);


        $messageForPersonnel = "you've received consultation request from {$this->client->getName()}";
        $event = new ConsultationRequestMutatedByParticipantEvent($consultationRequestId, $messageForPersonnel);
        $this->recordEvent($event);
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);
        
        $this->assertNoConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        $consultationRequest->accept();
        
        $consultationSessionId = Uuid::generateUuid4();
        $consultationSession = $consultationRequest->createConsultationSession($consultationSessionId);
        $this->consultationSessions->add($consultationSession);
        
        $messageForPersonnel = "consultation session with {$this->client->getName()} has been arranged";
        $event = new ConsultationSessionMutatedByParticipantEvent($consultationSessionId, $messageForPersonnel);
        $this->recordEvent($event);
    }

    protected function assertNoConsultationRequestInCollectionConflictedWith(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationRequest $otherConsultationRequest) use ($consultationRequest) {
            $inConflicStatus = new ConsultationRequestStatusVO('proposed');
            return
                    $otherConsultationRequest->conflictedWith($consultationRequest) &&
                    $otherConsultationRequest->statusEquals($inConflicStatus) &&
                    !$otherConsultationRequest->isConcluded();
        };
        if (!empty($this->consultationRequests->filter($p)->count())) {
            $errorDetail = "conlict: requested time already occupied by your other consultation request waiting for consultant response";
            throw RegularException::conflict($errorDetail);
        }
    }

    protected function assertNoConsultationSessioninCollectionConflictedWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->conflictedWithConsultationRequest($consultationRequest);
        };
        if (!empty($this->consultationSessions->filter($p)->count())) {
            $errorDetail = "conflict: requested time already occupied by your other consultation session";
            throw RegularException::conflict($errorDetail);
        }
    }

    protected function getConsultationRequestOrDie(string $consultationRequestId): ConsultationRequest
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $consultationRequestId));
        $consultationRequest = $this->consultationRequests->matching($criteria)->first();
        if (empty($consultationRequest)) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
        return $consultationRequest;
    }

}
