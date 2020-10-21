<?php

namespace Config;

class EventList
{
    const CLIENT_ACTIVATION_CODE_GENERATED = "client-activation-code-generated";
    const CLIENT_RESET_PASSWORD_CODE_GENERATED = "client-reset-password-code-generated";
    
    const USER_ACTIVATION_CODE_GENERATED = "user-activation-code-generated";
    const USER_RESET_PASSWORD_CODE_GENERATED = "user-reset-password-code-generated";

    const PROGRAM_REGISTRATION_SUBMITTED = "program-registration-submitted";
    const REGISTRANT_ACCEPTED = "registrant-accepted";
    
    const CONSULTATION_REQUEST_SUBMITTED = "consultation-request-submitted";
    const CONSULTATION_REQUEST_TIME_CHANGED = "consultation-request-time-changed";
    const CONSULTATION_REQUEST_CANCELLED = "consultation-request-cancelled";
    const OFFERED_CONSULTATION_REQUEST_ACCEPTED = "offered-consultation-request-accepted";
    
    const CONSULTATION_REQUEST_OFFERED = "consultation-request-offered";
    const CONSULTATION_REQUEST_REJECTED = "consultation-request-rejected";
    const CONSULTATION_SESSION_SCHEDULED_BY_CONSULTANT = "consultation-session-scheduled-by-consultant";
    
    const COMMENT_FROM_CONSULTANT_REPLIED = "comment-from-consultant-replied"; 
    const COMMENT_SUBMITTED_BY_CONSULTANT = "comment-submitted-by-consultant";
    
    const LEARNING_MATERIAL_VIEWED_BY_PARTICIPANT = "learning-material-viewed-by-participant";
    
}
