<?php

namespace Query\Application\Service\Firm\Program\ConsulationSetup;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ConsultationRequestRepository as InterfaceForClient,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestRepository as InterfaceForPersonnel,
    Application\Service\Firm\Team\ProgramParticipation\ConsultationRequestRepository as InterfaceForTeam,
    Application\Service\User\ProgramParticipation\ConsultationRequestRepository as InterfaceForUser,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

interface ConsultationRequestRepository extends InterfaceForPersonnel, InterfaceForClient, InterfaceForUser, InterfaceForTeam
{

    public function ofId(
            string $firmId, string $programId, string $consultationSetupId, string $consultationRequestId): ConsultationRequest;

    public function all(
            string $firmId, string $programId, string $consultationSetupId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
