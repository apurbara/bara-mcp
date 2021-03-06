<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Firm\Application\Service\Coordinator\ChangeConsultationSessionChannel;
use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession as ConsultationSession2;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Query\Application\Service\Firm\Program\ViewConsultationSession;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

class ConsultationSessionController extends AsProgramCoordinatorBaseController
{
    
    public function changeChannel($programId, $consultationSessionId)
    {
        $media = $this->stripTagsInputRequest("media");
        $address = $this->stripTagsInputRequest("address");
        
        $this->buildChangeChannelService()
                ->execute($this->firmId(), $this->personnelId(), $programId, $consultationSessionId, $media, $address);
        
        return $this->show($programId, $consultationSessionId);
    }
    
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($this->dateTimeImmutableOfQueryRequest("minStartTime"))
                ->setMaxEndTime($this->dateTimeImmutableOfQueryRequest("maxEndTime"))
                ->setContainConsultantFeedback($this->filterBooleanOfQueryRequest("containConsultantFeedback"))
                ->setContainParticipantFeedback($this->filterBooleanOfQueryRequest("containParticipantFeedback"));
        
        $consultationSessions = $service->showAll(
                $programId, $this->getPage(), $this->getPageSize(), $consultationSessionFilter);
        
        $result = [];
        $result["total"] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result["list"][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "media" => $consultationSession->getMedia(),
                "address" => $consultationSession->getAddress(),
                "consultationSetup" => [
                    "id" => $consultationSession->getConsultationSetup()->getId(),
                    "name" => $consultationSession->getConsultationSetup()->getName(),
                ],
                "consultant" => [
                    "id" => $consultationSession->getConsultant()->getId(),
                    "personnel" => [
                        "id" => $consultationSession->getConsultant()->getPersonnel()->getId(),
                        "name" => $consultationSession->getConsultant()->getPersonnel()->getName(),
                    ],
                ],
                "participant" => [
                    "id" => $consultationSession->getParticipant()->getId(),
                    "active" => $consultationSession->getParticipant()->isActive(),
                    "client" => $this->arrayDataOfClient($consultationSession->getParticipant()->getClientParticipant()),
                    "team" => $this->arrayDataOfTeam($consultationSession->getParticipant()->getTeamParticipant()),
                    "user" => $this->arrayDataOfUser($consultationSession->getParticipant()->getUserParticipant()),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($programId, $consultationSessionId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $consultationSession = $service->showById($programId, $consultationSessionId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }
    
    protected function arrayDataOfConsultationSession(ConsultationSession $consultationSession): array
    {
        $participantReport = empty($consultationSession->getParticipantFeedback())? null:
                (new FormRecordToArrayDataConverter())->convert($consultationSession->getParticipantFeedback());
        $consultantReport = empty($consultationSession->getConsultantFeedback())? null:
                (new FormRecordToArrayDataConverter())->convert($consultationSession->getConsultantFeedback());
        
        return [
            "id" => $consultationSession->getId(),
            "startTime" => $consultationSession->getStartTime(),
            "endTime" => $consultationSession->getEndTime(),
            "media" => $consultationSession->getMedia(),
            "address" => $consultationSession->getAddress(),
            "consultationSetup" => [
                "id" => $consultationSession->getConsultationSetup()->getId(),
                "name" => $consultationSession->getConsultationSetup()->getName(),
                "duration" => $consultationSession->getConsultationSetup()->getSessionDuration(),
            ],
            "consultant" => [
                "id" => $consultationSession->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationSession->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationSession->getConsultant()->getPersonnel()->getName(),
                ],
            ],
            "participant" => [
                "id" => $consultationSession->getParticipant()->getId(),
                "enrolledTime" => $consultationSession->getParticipant()->getEnrolledTimeString(),
                "active" => $consultationSession->getParticipant()->isActive(),
                "note" => $consultationSession->getParticipant()->getNote(),
                "client" => $this->arrayDataOfClient($consultationSession->getParticipant()->getClientParticipant()),
                "team" => $this->arrayDataOfTeam($consultationSession->getParticipant()->getTeamParticipant()),
                "user" => $this->arrayDataOfUser($consultationSession->getParticipant()->getUserParticipant()),
            ],
            "consultantReport" => $consultantReport,
            "participantReport" => $participantReport,
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant)? null: [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant)? null: [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant)? null: [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }
    
    protected function buildViewService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        return new ViewConsultationSession($consultationSessionRepository);
    }
    
    protected function buildChangeChannelService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        
        return new ChangeConsultationSessionChannel($consultationSessionRepository, $coordinatorRepository);
    }
}
