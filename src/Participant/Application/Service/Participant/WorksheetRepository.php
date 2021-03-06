<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\Worksheet;

interface WorksheetRepository
{

    public function nextIdentity(): string;

    public function add(Worksheet $worksheet): void;

    public function aWorksheetBelongsToClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId): Worksheet;

    public function aWorksheetBelongsToUserParticipant(
            string $userId, string $programParticipationId, string $worksheetId): Worksheet;
    
    public function aWorksheetBelongsToTeamParticipant(string $teamProgramParticipationId, string $worksheetId): Worksheet;
    
    public function update(): void;
    
    public function ofId(string $worksheetId): Worksheet;
}
