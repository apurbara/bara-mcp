<?php

namespace Query\Domain\Model\Firm\Manager;

use Query\Domain\Model\Firm\{
    Manager,
    Program\Activity,
    Program\Activity\Invitee,
    Program\Activity\Invitee\InviteeReport,
    Program\ActivityType\ActivityParticipant
};

class ManagerInvitee
{

    /**
     *
     * @var Manager
     */
    protected $manager;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Invitee
     */
    protected $invitee;

    function getManager(): Manager
    {
        return $this->manager;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    function getActivity(): Activity
    {
        return $this->invitee->getActivity();
    }

    function getActivityParticipant(): ActivityParticipant
    {
        return $this->invitee->getActivityParticipant();
    }

    function WillAttend(): ?bool
    {
        return $this->invitee->isWillAttend();
    }

    function isAttended(): ?bool
    {
        return $this->invitee->isAttended();
    }

    function isAnInitiator(): bool
    {
        return $this->invitee->isAnInitiator();
    }

    function isCancelled(): bool
    {
        return $this->invitee->isCancelled();
    }

    function getReport(): ?InviteeReport
    {
        return $this->invitee->getReport();
    }

}
