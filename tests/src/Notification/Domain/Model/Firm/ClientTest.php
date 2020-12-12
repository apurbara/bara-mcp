<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\ {
    Model\Firm,
    Model\Firm\Client\ClientMail,
    SharedModel\CanSendPersonalizeMail
};
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $firm;
    protected $client;
    protected $name;
    protected $mailGenerator;
    protected $mailMessage, $modifiedMail;
    protected $clientMailId = "clientMailId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestableClient();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->client->firm = $this->firm;
        $this->name = $this->buildMockOfClass(PersonName::class);
        $this->client->name = $this->name;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedMail = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_getName_returnFullName()
    {
        $this->name->expects($this->once())
                ->method('getFullName');
        $this->client->getFullName();
    }
    protected function executeRegisterAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("appendRecipientFirstNameInGreetings")
                ->willReturn($this->modifiedMail);
        
        $this->client->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerAsMailRecipient_modifiedMailMessageGreetings()
    {
        $this->name->expects($this->once())
                ->method("getFirstName")
                ->willReturn($firstName = "first name");
        
        $this->mailMessage->expects($this->once())
                ->method("appendRecipientFirstNameInGreetings")
                ->with($firstName);
        
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRecipient_addMailInMailGenerator()
    {
        $this->name->expects($this->once())
                ->method("getFullName")
                ->willReturn($fullName = "full name");
        $this->mailGenerator->expects($this->once())
                ->method("addMail")
                ->with($this->identicalTo($this->modifiedMail), $this->client->email, $fullName);
        $this->executeRegisterAsMailRecipient();
    }
    
    public function test_createActivationMail_returnClientMail()
    {
        $this->assertInstanceOf(ClientMail::class, $this->client->createActivationMail($this->clientMailId));
    }
    public function test_createResetPasswordMail_returnClientMail()
    {
        $this->assertInstanceOf(ClientMail::class, $this->client->createResetPasswordMail($this->clientMailId));
    }
}

class TestableClient extends Client
{
    public $firm;
    public $id;
    public $name;
    public $email = "client@email.org";
    
    function __construct()
    {
        parent::__construct();
    }
}
