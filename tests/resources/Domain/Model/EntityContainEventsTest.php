<?php

namespace Resources\Domain\Model;

use Resources\Application\Event\Event;
use Tests\TestBase;

class EntityContainEventsTest extends TestBase
{
    protected $model;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TestableEntityContainEvents();
        $this->event = $this->buildMockOfInterface(Event::class);
        $this->model->recordedEvents[] = $this->event;
    }
    
    public function test_recordEvent_addEventToRecordedEventsList()
    {
        $this->model->recordEvent($this->event);
        $this->assertEquals($this->event, $this->model->recordedEvents[1]);
    }
    
    protected function executePullRecordedEvent()
    {
        return $this->model->pullRecordedEvents();
    }
    
    public function test_pullRecordedEvents_returnRecordedEventsList()
    {
        $this->assertEquals($this->model->recordedEvents, $this->executePullRecordedEvent());
    }
    public function test_pullRecordedEvent_emptyRecordedEventList()
    {
        $this->executePullRecordedEvent();
        $this->assertEquals([], $this->model->recordedEvents);
    }
}

class TestableEntityContainEvents extends EntityContainEvents
{
    public $recordedEvents = [];
    
    function recordEvent(Event $event): void
    {
        parent::recordEvent($event);
    }
}
