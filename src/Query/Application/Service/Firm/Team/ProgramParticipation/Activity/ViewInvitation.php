<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

class ViewInvitation
{

    /**
     *
     * @var InvitationRepository
     */
    protected $invitationRepository;

    function __construct(InvitationRepository $invitationRepository)
    {
        $this->invitationRepository = $invitationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $teamId
     * @param string $activityId
     * @param int $page
     * @param int $pageSize
     * @return Invitee[]
     */
    public function showAll(string $firmId, string $teamId, string $activityId, int $page, int $pageSize)
    {
        return $this->invitationRepository->allInvitationsInTeamParticipantActivity(
                        $firmId, $teamId, $activityId, $page, $pageSize);
    }

    public function showById(string $firmId, string $teamId, string $invitationId): Invitee
    {
        return $this->invitationRepository->anInvitationFromTeam($firmId, $teamId, $invitationId);
    }

}
