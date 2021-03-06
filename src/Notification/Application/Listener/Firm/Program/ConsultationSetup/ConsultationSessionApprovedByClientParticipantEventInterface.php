<?php

namespace Notification\Application\Listener\Firm\Program\ConsultationSetup;

use Resources\Application\Event\Event;

interface ConsultationSessionApprovedByClientParticipantEventInterface extends Event
{

    public function getFirmId(): string;

    public function getClientId(): string;

    public function getProgramParticipationId(): string;

    public function getConsultationSessionId(): string;
}
