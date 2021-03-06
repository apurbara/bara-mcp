<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\ {
    Firm,
    Firm\Program\ActivityType,
    Shared\Form,
    Shared\FormData
};
use Tests\TestBase;

class FeedbackFormTest extends TestBase
{
    protected $firm;
    protected $form;
    protected $feedbackForm;
    
    protected $id = 'consultation-feedback-form-id';
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->form = $this->buildMockOfClass(Form::class);
        
        $this->feedbackForm = new TestableFeedbackForm($this->firm, 'id', $this->form);
        
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    public function test_construct_setProperties()
    {
        $feedbackForm = new TestableFeedbackForm($this->firm, $this->id, $this->form);
        $this->assertEquals($this->firm, $feedbackForm->firm);
        $this->assertEquals($this->id, $feedbackForm->id);
        $this->assertEquals($this->form, $feedbackForm->form);
    }
    
    public function test_update_updateForm()
    {
        $this->form->expects($this->once())
            ->method('update')
            ->with($this->formData);
        $this->feedbackForm->update($this->formData);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->feedbackForm->remove();
        $this->assertTrue($this->feedbackForm->removed);
    }
    
    public function test_belongsToSameFirmAs_returnActivityTypeBelongsToFirmResult()
    {
        $activityType = $this->buildMockOfClass(ActivityType::class);
        $activityType->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->feedbackForm->belongsToSameFirmAs($activityType);
    }
    
    public function test_belongsToFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->feedbackForm->belongsToFirm($this->feedbackForm->firm));
    }
    public function test_belongsToFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->feedbackForm->belongsToFirm($firm));
    }
    
}

class TestableFeedbackForm extends FeedbackForm
{
    public $firm, $id, $form, $removed;
}
