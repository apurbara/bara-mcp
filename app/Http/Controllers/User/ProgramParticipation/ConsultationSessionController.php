<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use App\Http\Controllers\User\UserBaseController;
use Participant\Application\Service\UserParticipant\ConsultationSession\SubmitFeedback;
use Participant\Domain\Model\Participant\ConsultationSession as ConsultationSession2;
use Participant\Domain\Service\UserFileInfoFinder;
use Query\Application\Service\User\ProgramParticipation\ViewConsultationSession;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ParticipantFeedback;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class ConsultationSessionController extends UserBaseController
{

    public function submitReport($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildSetParticipantFeedbackService();
        $formRecordData = $this->getFormRecordData();
        $mentorRating = $this->stripTagsInputRequest("mentorRating");
        
        $service->execute($this->userId(), $programParticipationId, $consultationSessionId, $formRecordData, $mentorRating);

        return $this->show($programParticipationId, $consultationSessionId);
    }

    public function show($programParticipationId, $consultationSessionId)
    {
        $service = $this->buildViewService();
        $consultationSession = $service->showById($this->userId(), $programParticipationId, $consultationSessionId);

        return $this->singleQueryResponse($this->arrayDataOfConsultationSession($consultationSession));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();

        $minStartTime = $this->dateTimeImmutableOfQueryRequest("minStartTime");
        $maxEndTime = $this->dateTimeImmutableOfQueryRequest("maxEndTime");
        $containParticipantFeedback = $this->filterBooleanOfQueryRequest("containParticipantFeedback");
        $containConsultantFeedback = $this->filterBooleanOfQueryRequest('containConsultantFeedback');

        $consultationSessionFilter = (new ConsultationSessionFilter())
                ->setMinStartTime($minStartTime)
                ->setMaxEndTime($maxEndTime)
                ->setContainParticipantFeedback($containParticipantFeedback)
                ->setContainConsultantFeedback($containConsultantFeedback);

        $consultationSessions = $service->showAll($this->userId(), $programParticipationId, $this->getPage(),
                $this->getPageSize(), $consultationSessionFilter);

        $result = [];
        $result['total'] = count($consultationSessions);
        foreach ($consultationSessions as $consultationSession) {
            $result['list'][] = [
                "id" => $consultationSession->getId(),
                "startTime" => $consultationSession->getStartTime(),
                "endTime" => $consultationSession->getEndTime(),
                "media" => $consultationSession->getMedia(),
                "address" => $consultationSession->getAddress(),
                "hasParticipantFeedback" => $consultationSession->hasParticipantFeedback(),
                "consultationSetup" => [
                    "id" => $consultationSession->getConsultationSetup()->getId(),
                    "name" => $consultationSession->getConsultationSetup()->getName()
                ],
                "consultant" => [
                    "id" => $consultationSession->getConsultant()->getId(),
                    "personnel" => [
                        "id" => $consultationSession->getConsultant()->getPersonnel()->getId(),
                        "name" => $consultationSession->getConsultant()->getPersonnel()->getName()
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultationSession(ConsultationSession $consultationSession)
    {
        return [
            "id" => $consultationSession->getId(),
            "startTime" => $consultationSession->getStartTime(),
            "endTime" => $consultationSession->getEndTime(),
            "media" => $consultationSession->getMedia(),
            "address" => $consultationSession->getAddress(),
            "consultationSetup" => [
                "id" => $consultationSession->getConsultationSetup()->getId(),
                "name" => $consultationSession->getConsultationSetup()->getName(),
                "participantFeedbackForm" => $this->arrayDataOfFeedbackForm(
                        $consultationSession->getConsultationSetup()->getParticipantFeedbackForm()),
            ],
            "consultant" => [
                "id" => $consultationSession->getConsultant()->getId(),
                "personnel" => [
                    "id" => $consultationSession->getConsultant()->getPersonnel()->getId(),
                    "name" => $consultationSession->getConsultant()->getPersonnel()->getName()
                ],
            ],
            "participantFeedback" => $this->arrayDataOfParticipantFeedback($consultationSession->getParticipantFeedback()),
        ];
    }
    protected function arrayDataOfFeedbackForm(FeedbackForm $feedbackForm): array
    {
        $data = (new FormToArrayDataConverter())->convert($feedbackForm);
        $data['id'] = $feedbackForm->getId();
        return $data;
    }
    protected function arrayDataOfParticipantFeedback(?ParticipantFeedback $participantFeedback): ?array
    {
        if (empty($participantFeedback)) {
            return null;
        }
        $result = (new FormRecordToArrayDataConverter())->convert($participantFeedback);
        $result["mentorRating"] = $participantFeedback->getMentorRating();
        return $result;
    }

    protected function buildViewService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession::class);
        return new ViewConsultationSession($consultationSessionRepository);
    }

    protected function buildSetParticipantFeedbackService()
    {
        $consultationSessionRepository = $this->em->getRepository(ConsultationSession2::class);
        return new SubmitFeedback($consultationSessionRepository);
    }

    protected function getFormRecordData()
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new UserFileInfoFinder($fileInfoRepository, $this->userId());
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

}
