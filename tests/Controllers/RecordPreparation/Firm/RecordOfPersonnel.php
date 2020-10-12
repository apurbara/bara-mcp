<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    JwtHeaderTokenGenerator,
    Record,
    RecordOfFirm,
    TestablePassword
};

class RecordOfPersonnel implements Record
{
    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    public $id, $firstName, $lastName, $email, $password, $phone = "", $joinTime, $removed = false;
    public $bio;
    public $rawPassword;
    public $token;
    
    public function __construct(RecordOfFirm $firm, $index)
    {
        $this->rawPassword = "Password12345";
        
        $this->firm = $firm;
        $this->id = "personnel-$index-id";
        $this->firstName = "personnel $index firstname";
        $this->lastName = "personnel $index lastname";
        $this->email = "personnel_$index@barapraja.com";
        $this->password = (new TestablePassword($this->rawPassword))->getHashedPassword();
        $this->phone = "";
        $this->bio = "personnel $index bio";
        $this->joinTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->removed = false;
        
        $data = [
            "firmId" => $this->firm->id,
            "personnelId" => $this->id,
        ];
        $this->token = JwtHeaderTokenGenerator::generate($data);
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "email" => $this->email,
            "password" => $this->password,
            "phone" => $this->phone,
            "bio" => $this->bio,
            "joinTime" => $this->joinTime,
            "removed" => $this->removed,
        ];
    }
    
    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }

}
