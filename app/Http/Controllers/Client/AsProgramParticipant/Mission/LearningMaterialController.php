<?php

namespace App\Http\Controllers\Client\AsProgramParticipant\Mission;

use App\Http\Controllers\Client\AsProgramParticipant\AsProgramParticipantBaseController;
use Query\ {
    Application\Service\Firm\Program\Mission\ViewLearningMaterial,
    Domain\Model\Firm\Program\Mission\LearningMaterial
};

class LearningMaterialController extends AsProgramParticipantBaseController
{

    public function show($programId, $missionId, $learningMaterialId)
    {
        $this->authorizedClientIsActiveProgramParticipant($this->firmId(), $programId);
        
        $service = $this->buildViewService();
        $learningMaterial = $service->showById($this->firmId(), $programId, $missionId, $learningMaterialId);
        
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function showAll($programId, $missionId)
    {
        $this->authorizedClientIsActiveProgramParticipant($this->firmId(), $programId);
        
        $service = $this->buildViewService();
        $learningMaterials = $service->showAll($this->firmId(), $programId, $missionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($learningMaterials);
        foreach ($learningMaterials as $learningMaterial) {
            $result['list'][] = $this->arrayDataOfLearningMaterial($learningMaterial);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfLearningMaterial(LearningMaterial $learningMaterial): array
    {
        return [
            "id" => $learningMaterial->getId(),
            "name" => $learningMaterial->getName(),
            "content" => $learningMaterial->getContent(),
        ];
    }

    protected function buildViewService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new ViewLearningMaterial($learningMaterialRepository);
    }

}
