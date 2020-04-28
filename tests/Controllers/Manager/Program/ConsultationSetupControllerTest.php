<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\RecordOfConsultationSetup,
    RecordPreparation\Firm\RecordOfConsultationFeedbackForm,
    RecordPreparation\Shared\RecordOfForm
};


class ConsultationSetupControllerTest extends ProgramTestCase
{

    protected $consultationSetupUri;
    protected $consultationSetup;
    protected $consultationSetupOne;
    protected $participantFeedbackFormTwo;
    protected $consultantFeedbackFormTwo;
    protected $consultationSetupInput = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetupUri = $this->programUri . "/{$this->program->id}/consultation-setups";
        $this->connection->table("Form")->truncate();
        $this->connection->table("ConsultationFeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();

        $participantConsultationFeedbackForm = new RecordOfConsultationFeedbackForm(
                $this->firm, $participantForm = new RecordOfForm('participant-form-0'));
        $participantConsultationFeedbackFormOne = new RecordOfConsultationFeedbackForm(
                $this->firm, $participantFormOne = new RecordOfForm('participant-form-1'));
        $this->participantFeedbackFormTwo = new RecordOfConsultationFeedbackForm(
                $this->firm, $participantFormTwo = new RecordOfForm('participant-form-2'));
        $consultantConsultationFeedbackForm = new RecordOfConsultationFeedbackForm(
                $this->firm, $consultantForm = new RecordOfForm('consultant-form-0'));
        $consultantConsultationFeedbackFormOne = new RecordOfConsultationFeedbackForm(
                $this->firm, $consultantFormOne = new RecordOfForm('consultant-form-1'));
        $this->consultantFeedbackFormTwo = new RecordOfConsultationFeedbackForm(
                $this->firm, $consultantFormTwo = new RecordOfForm('consultant-form-2'));
        
        $this->connection->table("Form")->insert($participantForm->toArrayForDbEntry());
        $this->connection->table("Form")->insert($participantFormOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($participantFormTwo->toArrayForDbEntry());
        $this->connection->table("Form")->insert($consultantForm->toArrayForDbEntry());
        $this->connection->table("Form")->insert($consultantFormOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($consultantFormTwo->toArrayForDbEntry());
        
        $this->connection->table("ConsultationFeedbackForm")->insert($participantConsultationFeedbackForm->toArrayForDbEntry());
        $this->connection->table("ConsultationFeedbackForm")->insert($participantConsultationFeedbackFormOne->toArrayForDbEntry());
        $this->connection->table("ConsultationFeedbackForm")->insert($this->participantFeedbackFormTwo->toArrayForDbEntry());
        $this->connection->table("ConsultationFeedbackForm")->insert($consultantConsultationFeedbackForm->toArrayForDbEntry());
        $this->connection->table("ConsultationFeedbackForm")->insert($consultantConsultationFeedbackFormOne->toArrayForDbEntry());
        $this->connection->table("ConsultationFeedbackForm")->insert($this->consultantFeedbackFormTwo->toArrayForDbEntry());

        $this->consultationSetup = new RecordOfConsultationSetup(
                $this->program, $participantConsultationFeedbackForm, $consultantConsultationFeedbackForm, 0);
        $this->consultationSetupOne = new RecordOfConsultationSetup(
                $this->program, $participantConsultationFeedbackFormOne, $consultantConsultationFeedbackFormOne, 1);
        $this->connection->table("ConsultationSetup")->insert($this->consultationSetup->toArrayForDbEntry());
        $this->connection->table("ConsultationSetup")->insert($this->consultationSetupOne->toArrayForDbEntry());

        $this->consultationSetupInput = [
            "name" => "new consultationSetup name",
            "sessionDuration" => 90,
            "participantFeedbackFormId" => $this->participantFeedbackFormTwo->id,
            "consultantFeedbackFormId" => $this->consultantFeedbackFormTwo->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ConsultationFeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
    }

    public function test_add()
    {
        $response = [
            "name" => $this->consultationSetupInput['name'],
            "sessionDuration" => $this->consultationSetupInput['sessionDuration'],
            "participantFeedbackForm" => [
                "id" => $this->participantFeedbackFormTwo->id,
                "name" => $this->participantFeedbackFormTwo->form->name,
            ],
            "consultantFeedbackForm" => [
                "id" => $this->consultantFeedbackFormTwo->id,
                "name" => $this->consultantFeedbackFormTwo->form->name,
            ],
        ];

        $this->post($this->consultationSetupUri, $this->consultationSetupInput, $this->manager->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $consultationSetupRecord = [
            "Program_id" => $this->program->id,
            "name" => $this->consultationSetupInput['name'],
            "sessionDuration" => $this->consultationSetupInput['sessionDuration'],
            "ConsultationFeedbackForm_idForParticipant" => $this->consultationSetupInput['participantFeedbackFormId'],
            "ConsultationFeedbackForm_idForConsultant" => $this->consultationSetupInput['consultantFeedbackFormId'],
            "removed" => false,
        ];
        $this->seeInDatabase('ConsultationSetup', $consultationSetupRecord);
    }
    public function test_add_emptyName_error400()
    {
        $this->consultationSetupInput['name'] = '';
        $this->post($this->consultationSetupUri, $this->consultationSetupInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_add_emptySessionDuration_error400()
    {
        $this->consultationSetupInput['sessionDuration'] = 0;
        $this->post($this->consultationSetupUri, $this->consultationSetupInput, $this->manager->token)
                ->seeStatusCode(400);
    }

    public function test_add_userNotAdmin_error401()
    {
        $this->post($this->consultationSetupUri, $this->consultationSetupInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_remove()
    {
        $uri = $this->consultationSetupUri . "/{$this->consultationSetup->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);

        $consultationSetupRecord = [
            "id" => $this->consultationSetup->id,
            "removed" => true,
        ];
        $this->seeInDatabase('ConsultationSetup', $consultationSetupRecord);
    }

    public function test_remove_userNotAdmin_error401()
    {
        $uri = $this->consultationSetupUri . "/{$this->consultationSetup->id}";
        $this->delete($uri, [], $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_show()
    {
        $response = [
            "id" => $this->consultationSetup->id,
            "name" => $this->consultationSetup->name,
            "sessionDuration" => $this->consultationSetup->sessionDuration,
            "participantFeedbackForm" => [
                "id" => $this->consultationSetup->participantConsultationFeedbackForm->id,
                "name" => $this->consultationSetup->participantConsultationFeedbackForm->form->name,
            ],
            "consultantFeedbackForm" => [
                "id" => $this->consultationSetup->consultantConsultationFeedbackForm->id,
                "name" => $this->consultationSetup->consultantConsultationFeedbackForm->form->name,
            ],
        ];
        $uri = $this->consultationSetupUri . "/{$this->consultationSetup->id}";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_show_userNotAdmin_error401()
    {
        $uri = $this->consultationSetupUri . "/{$this->consultationSetup->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultationSetup->id,
                    "name" => $this->consultationSetup->name,
                ],
                [
                    "id" => $this->consultationSetupOne->id,
                    "name" => $this->consultationSetupOne->name,
                ],
            ],
        ];
        $this->get($this->consultationSetupUri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_showAll_userNotAdmin_error401()
    {
        $this->get($this->consultationSetupUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }

}
