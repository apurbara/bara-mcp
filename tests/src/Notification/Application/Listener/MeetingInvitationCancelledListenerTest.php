<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingInvitationCancelledNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class MeetingInvitationCancelledListenerTest extends TestBase
{
    protected $generateMeetingInvitationCancelledNotification;
    protected $sendImmediateMail;
    protected $listener;
    protected $event, $meetingAttendeeId = "meetingAttendeeId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->generateMeetingInvitationCancelledNotification = $this->buildMockOfClass(GenerateMeetingInvitationCancelledNotification::class);
        $this->sendImmediateMail = $this->buildMockOfClass(SendImmediateMail::class);
        
        $this->listener = new MeetingInvitationCancelledListener($this->generateMeetingInvitationCancelledNotification, $this->sendImmediateMail);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_generateMeetingInvitationCancelledNotification()
    {
        $this->event->expects($this->once())->method("getId")->willReturn($this->meetingAttendeeId);
        $this->generateMeetingInvitationCancelledNotification->expects($this->once())
                ->method("execute")
                ->with($this->meetingAttendeeId);
        $this->executeHandle();
    }
    public function test_handle_sendImmediateMail()
    {
        $this->sendImmediateMail->expects($this->once())
                ->method("execute");
        $this->executeHandle();
    }
}
