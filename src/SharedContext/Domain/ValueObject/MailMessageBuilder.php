<?php

namespace SharedContext\Domain\ValueObject;

class MailMessageBuilder
{

    const CONSULTATION_REQUESTED = 11;
    const CONSULTATION_SCHEDULE_CHANGED = 12;
    const CONSULTATION_CANCELLED = 13;
    const CONSULTATION_REJECTED = 14;
    const CONSULTATION_ACCEPTED = 15;
    const MEETING_CREATED = 21;
    const MEETING_INVITATION_SENT = 22;
    const MEETING_INVITATION_CANCELLED = 23;
    const MEETING_SCHEDULE_CHANGED = 24;

    public static function buildAccountActivationMailMessage(?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "Activate Account";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
Your new account is almost ready. 
Visit the following link to activate your account.
_MESSAGE;
        
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }
    
    public static function buildAccountResetPasswordMailMessage(?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "Activate Account";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
We received a request to reset your password.
Visit the following link to change your password.

Ignore this message if you never made this request.
_MESSAGE;
        
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

    public static function buildConsultationRequestMailMessageForMentor(
            int $state, ?string $participantName, ?string $timeDescription, ?string $media, ?string $location,
            ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::CONSULTATION_REQUESTED:
                $subject = "New Consultation Request";
                $introductionMessage = "Participant {$participantName} requested new consultation";
                break;
            case self::CONSULTATION_SCHEDULE_CHANGED:
                $subject = "Consultation Request Schedule Changed";
                $introductionMessage = "Participant {$participantName} changed consultation request schedule";
                break;
            case self::CONSULTATION_CANCELLED:
                $subject = "Consultation Request Cancelled";
                $introductionMessage = "Participant {$participantName} cancelled consultation request";
                break;
            case self::CONSULTATION_ACCEPTED:
                $subject = "Consultation Scheduled";
                $introductionMessage = "Participant {$participantName} accepted offered consultation schedule";
                break;
            default:
                break;
        }

        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
{$introductionMessage}
    schedule: {$timeDescription}
    media: {$media}
    location: {$location}
    
You can view consultation detail or give response in the following link
_MESSAGE;
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

    public static function buildConsultationRequestMailMessageForOtherTeamMember(
            int $state, ?string $mentorName, ?string $memberName, ?string $teamName, ?string $timeDescription,
            ?string $media, ?string $location, ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::CONSULTATION_REQUESTED:
                $subject = "New Consultation Request";
                $introductionMessage = "Partner {$memberName} from team {$teamName} requested new consultation to mentor {$mentorName}";
                break;
            case self::CONSULTATION_SCHEDULE_CHANGED:
                $subject = "Consultation Request Schedule Changed";
                $introductionMessage = "Partner {$memberName} from team {$teamName} changed consultation request schedule to mentor {$mentorName}";
                break;
            case self::CONSULTATION_CANCELLED:
                $subject = "Consultation Request Cancelled";
                $introductionMessage = "Partner {$memberName} from team {$teamName} cancelled consultation request to mentor {$mentorName}";
                break;
            case self::CONSULTATION_ACCEPTED:
                $subject = "Consultation Scheduled";
                $introductionMessage = "Partner {$memberName} from team {$teamName} accepted consultation scheduled offered by mentor {$mentorName}";
                break;
            default:
                break;
        }

        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
{$introductionMessage}
    schedule: {$timeDescription}
    media: {$media}
    location: {$location}
    
You can view consultation detail or give response in the following link
_MESSAGE;
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

    public static function buildConsultationRequestMailMessageForParticipant(
            int $state, ?string $mentorName, ?string $timeDescription, ?string $media, ?string $location,
            ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::CONSULTATION_SCHEDULE_CHANGED:
                $subject = "Consultation Request Schedule Changed";
                $introductionMessage = "Mentor {$mentorName} offered consultation schedule";
                break;
            case self::CONSULTATION_REJECTED:
                $subject = "Consultation Request Rejected";
                $introductionMessage = "Mentor {$mentorname} rejected consultation request";
                break;
            case self::CONSULTATION_ACCEPTED:
                $subject = "Consultation Scheduled";
                $introductionMessage = "Mentor {$mentorname} accepted consultation request";
                break;
            default:
                break;
        }

        $greetings = "Hi Participant";
        $mainMessage = <<<_MESSAGE
{$introductionMessage}
    schedule: {$timeDescription}
    media: {$media}
    location: {$location}
    
You can view consultation detail or give response in the following link
_MESSAGE;
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

    public static function buildMeetingMailMessage(
            int $state, ?string $meetingType, ?string $meetingName, ?string $meetingDescription,
            ?string $timeDescription, ?string $location, ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::MEETING_CREATED:
                $subject = "New Meeting Schedule";
                $introductionMessage = "You or your partner scheduled a new meeting";
                break;
            case self::MEETING_INVITATION_SENT:
                $subject = "Meeting Invitation";
                $introductionMessage = "You are invited to a meeting";
                break;
            case self::MEETING_INVITATION_CANCELLED:
                $subject = "Meeting Invitation Cancelled";
                $introductionMessage = "Your meeting invitation has been cancelled";
                break;
            case self::MEETING_SCHEDULE_CHANGED:
                $subject = "Meeting Schedule Changed";
                $introductionMessage = "Meeting schedule has been changed";
                break;
            default:
                break;
        }
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
{$introductionMessage}
    type: {$meetingType}
    meeting: {$meetingName}: {$meetingDescription}
    schedule: {$timeDescription}
    location: {$location}
    
You can view consultation detail or give response in the following link
_MESSAGE;
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

    public static function buildWorksheetCommentMailMessageForParticipant(
            ?string $mentorName, ?string $worksheetType, ?string $worksheetName, ?string $message, ?string $domain,
            ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "New Comment";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
Mentor {$mentorName} commented on your worksheet
    worksheet: {$worksheetType} - {$worksheetName}
    message: {$message}
    
You can view comment detail in the following link
_MESSAGE;

        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

    public static function buildWorksheetCommentMailMessageForMentor(
            ?string $participantName, ?string $worksheetType, ?string $worksheetName, ?string $message, ?string $domain,
            ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "Comment Replied";
        $greetings = "Hi";
        $mainMessage = <<<_MESSAGE
Participant {$participantName} replied to your comment
    worksheet: {$worksheetType} - {$worksheetName}
    message: {$message}
    
You can view comment detail in the following link
_MESSAGE;

        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

}
