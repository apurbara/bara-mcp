<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

interface InvitationRepository
{

    public function anInvitationFromTeam(string $firmId, string $teamId, string $invitationId): Invitee;

    public function allInvitationsInTeamParticipantActivity(
            string $firmId, string $teamId, string $activityId, int $page, int $pageSize);
}
