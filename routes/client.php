<?php

$clientAggregate = [
    'prefix' => '/api/client',
    'namespace' => 'Client',
    'middleware' => 'clientJwtAuth',
];
$router->group($clientAggregate, function () use ($router) {
    $router->patch("/update-profile", ["uses" => "AccountController@updateProfile"]);
    $router->patch("/change-password", ["uses" => "AccountController@changePassword"]);
    
    $router->group(['prefix' => '/program-registrations'], function () use($router) {
        $controller = "ProgramRegistrationController";
        $router->post("", ["uses" => "$controller@apply"]);
        $router->patch("/{programRegistrationId}/cancel", ["uses" => "$controller@cancel"]);
        $router->get("/{programRegistrationId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/program-participations'], function () use($router) {
        $controller = "ProgramParticipationController";
        $router->patch("/{programParticipationId}/quit", ["uses" => "$controller@quit"]);
        $router->get("/{programParticipationId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $programParticipationAggregate = [
        'prefix' => '/program-participations/{programParticipationId}',
        'namespace' => 'ProgramParticipation',
    ];
    $router->group($programParticipationAggregate, function () use ($router) {
        $router->post('/file-uploads', ['uses' => "FileUploadController@upload"]);
        $router->delete('/participant-comments/{participantCommentId}', ['uses' => "ParticipantCommentController@remove"]);
        
        $router->group(['prefix' => '/consultation-requests'], function () use($router) {
            $controller = "ConsultationRequestController";
            $router->post("", ["uses" => "$controller@propose"]);
            $router->patch("/{consultationRequestId}/cancel", ["uses" => "$controller@cancel"]);
            $router->patch("/{consultationRequestId}/repropose", ["uses" => "$controller@rePropose"]);
            $router->patch("/{consultationRequestId}/accept", ["uses" => "$controller@accept"]);
            $router->get("/{consultationRequestId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/consultation-sessions'], function () use($router) {
            $controller = "ConsultationSessionController";
            $router->put("/{consultationSessionId}/participant-feedback", ["uses" => "$controller@setParticipantFeedback"]);
            $router->get("/{consultationSessionId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/worksheets'], function () use($router) {
            $controller = "WorksheetController";
            $router->post("", ["uses" => "$controller@addRoot"]);
            $router->post("/{worksheetId}", ["uses" => "$controller@addBranch"]);
            $router->patch("/{worksheetId}", ["uses" => "$controller@update"]);
            $router->delete("/{worksheetId}", ["uses" => "$controller@remove"]);
            $router->get("/{worksheetId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $worksheetAggregate = [
            'prefix' => '/worksheets/{worksheetId}',
            'namespace' => 'Worksheet',
        ];
        $router->group($worksheetAggregate, function () use ($router) {
            $router->group(['prefix' => '/comments'], function () use($router) {
                $controller = "CommentController";
                $router->post("", ["uses" => "$controller@submitNew"]);
                $router->post("/{commentId}", ["uses" => "$controller@submitReply"]);
                $router->get("/{commentId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
        });
        
    });
    
    $asProgramParticipantAggregate = [
        'prefix' => '/as-program-participant/{firmId}/{programId}',
        'namespace' => 'AsProgramParticipant',
    ];
    $router->group($asProgramParticipantAggregate, function () use ($router) {
        $router->group(['prefix' => '/consultants'], function () use($router) {
            $controller = "ConsultantController";
            $router->get("/{consultantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/missions'], function () use($router) {
            $controller = "MissionController";
            $router->get("/{missionId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
    });
});
