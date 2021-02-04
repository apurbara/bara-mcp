<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Service\DataFinder;
use Query\Domain\Service\LearningMaterialFinder;
use Resources\Application\Event\ContainEvents;

class ClientParticipant implements ContainEvents
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
     * @var Participant
     */
    protected $participant;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    public function getProgram(): Program
    {
        return $this->participant->getProgram();
    }

    public function getEnrolledTimeString(): string
    {
        return $this->participant->getEnrolledTimeString();
    }

    public function isActive(): bool
    {
        return $this->participant->isActive();
    }

    public function getNote(): ?string
    {
        return $this->participant->getNote();
    }
    
    public function getMetricAssignment():?MetricAssignment
    {
        return $this->participant->getMetricAssignment();
    }

    public function viewLearningMaterial(LearningMaterialFinder $learningMaterialFinder, string $learningMaterialId): LearningMaterial
    {
        return $this->participant->viewLearningMaterial($learningMaterialFinder, $learningMaterialId);
    }

    public function pullRecordedEvents(): array
    {
        return $this->participant->pullRecordedEvents();
    }
    
    public function viewSummary(DataFinder $dataFinder): array
    {
        return $this->participant->viewSummary($dataFinder);
    }

}
