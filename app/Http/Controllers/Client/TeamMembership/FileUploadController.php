<?php

namespace App\Http\Controllers\Client\TeamMembership;

use App\Http\Controllers\FlySystemUploadFileBuilder;
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ViewTeamFileInfo,
    Domain\Model\Firm\Team\TeamFileInfo,
    Domain\Service\Firm\Team\TeamFileInfoFinder
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Team\ {
    Application\Service\Firm\Client\TeamMembership\UploadTeamFile,
    Domain\Model\Team\Member,
    Domain\Model\Team\TeamFileInfo as TeamFileInfo2
};

class FileUploadController extends TeamMembershipBaseController
{
    public function upload($teamMembershipId)
    {
        $service = $this->buildUploadService();
        
        $name = strip_tags($this->request->header('fileName'));
        $size = filter_var($this->request->header('Content-Length'), FILTER_SANITIZE_NUMBER_FLOAT);
        $fileInfoData = new FileInfoData($name, floatval($size));
        $fileInfoData->addFolder("client_{$this->clientId()}");
        $fileInfoData->addFolder("teamMembership_{$teamMembershipId}");

        $contents = fopen('php://input', 'r');
        
        $teamFileInfoId = $service->execute($this->firmId(), $this->clientId(), $teamMembershipId, $fileInfoData, $contents);
        
        if (is_resource($contents)) {
            fclose($contents);
        }
        
        $viewService = $this->buildViewService();
        $teamFileInfo = $viewService->showById($this->firmId(), $this->clientId(), $teamMembershipId, $teamFileInfoId);
        return $this->commandCreatedResponse($this->arrayDataOfTeamFileInfo($teamFileInfo));
    }
    
    protected function arrayDataOfTeamFileInfo(TeamFileInfo $teamFileInfo): array
    {
        return [
            "id" => $teamFileInfo->getId(),
            "path" => $teamFileInfo->getFullyQualifiedFileName(),
            "size" => $teamFileInfo->getSize(),
        ];
    }
    protected function buildViewService()
    {
        $teamFileInfoRepository = $this->em->getRepository(TeamFileInfo::class);
        $teamFileInfoFinder = new TeamFileInfoFinder($teamFileInfoRepository);
        return new ViewTeamFileInfo($this->teamMembershipRepository(), $teamFileInfoFinder);
    }
    protected function buildUploadService()
    {
        $teamFileInfoRepository = $this->em->getRepository(TeamFileInfo2::class);
        $teamMembershipRepository = $this->em->getRepository(Member::class);
        $uploadFile = FlySystemUploadFileBuilder::build();
        return new UploadTeamFile($teamFileInfoRepository, $teamMembershipRepository, $uploadFile);
    }
}
