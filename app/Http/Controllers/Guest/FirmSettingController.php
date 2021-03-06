<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Query\ {
    Application\Service\FirmView,
    Domain\Model\Firm
};

class FirmSettingController extends Controller
{
    public function show($firmIdentifier)
    {
        $firmRepository = $this->em->getRepository(Firm::class);
        $service = new FirmView($firmRepository);
        
        $firm = $service->showByIdentifier($firmIdentifier);
        $result = [
            "logoPath" => !empty($firm->getLogo())? $firm->getLogo()->getFullyQualifiedFileName(): null,
            "displaySetting" => $firm->getDisplaySetting(),
        ];
        return $this->singleQueryResponse($result);
    }
}
