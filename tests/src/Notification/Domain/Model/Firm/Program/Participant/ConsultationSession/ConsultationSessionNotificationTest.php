<?php

namespace Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\Notification;
use Tests\TestBase;

class ConsultationSessionNotificationTest extends TestBase
{
    protected $consultationSession;
    protected $notification;
    protected $consultationSessionNotification;
    protected $id = "newId", $message = "new message";
    
    protected $user;
    protected $client;
    protected $personnel;
    protected $coordinator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionNotification = new TestableConsultationSessionNotification($this->consultationSession, "id", "message");
        
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->consultationSessionNotification->notification = $this->notification;
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultationSessionNotification = new TestableConsultationSessionNotification($this->consultationSession, $this->id, $this->message);
        $this->assertEquals($this->consultationSession, $consultationSessionNotification->consultationSession);
        $this->assertEquals($this->id, $consultationSessionNotification->id);
        $notification = new Notification($this->id, $this->message);
        $this->assertEquals($notification, $consultationSessionNotification->notification);
    }
    
    public function test_addUserRecipient_executeNotificationsAddUserRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addUserRecipient");
        $this->consultationSessionNotification->addUserRecipient($this->user);
    }
    public function test_addClientRecipient_executeNotificationsAddClientRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addClientRecipient");
        $this->consultationSessionNotification->addClientRecipient($this->client);
    }
    public function test_addPersonnelRecipient_executeNotificationsAddPersonnelRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addPersonnelRecipient");
        $this->consultationSessionNotification->addPersonnelRecipient($this->personnel);
    }
    
    public function test_addCoordinatorAsRecipient_addCoordinatorAsNotificationRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addCoordinatorRecipient");
        $this->consultationSessionNotification->addCoordinatorAsRecipient($this->coordinator);
    }
}

class TestableConsultationSessionNotification extends ConsultationSessionNotification
{
    public $consultationSession;
    public $id;
    public $notification;
}
