<?php

namespace Tests\Controllers\Client\TeamMembership;

use Illuminate\Http\UploadedFile;
use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};
use Tests\Controllers\Client\TeamMembershipTestCase;

class FileUploadControllerTest extends TeamMembershipTestCase
{
    protected $fileUploadUri;
    protected $fileUploadInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadUri = $this->teamMembershipUri. "/file-uploads";
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('TeamFileInfo')->truncate();
        
        $root = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("client_{$this->client->id}");
        
        $this->fileUploadInput = [
            new UploadedFile(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'cat_pile.jpg', 'cat_pile.jpg'),
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('TeamFileInfo')->truncate();
        
        $root = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $filessystem->deleteDir("client_{$this->client->id}");
    }
    
    public function test_upload()
    {
        $header = $this->teamMembership->client->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(201);
        
        $teamFileInfo = [
            "Team_id" => $this->teamMembership->team->id,
            "removed" => false,
        ];
        $this->seeInDatabase("TeamFileInfo", $teamFileInfo);
        
        $fileInfoEntry = [
            "name" => 'cat_pile.jpg',
        ];
        $this->seeInDatabase('FileInfo', $fileInfoEntry);
    }
    public function test_upload_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        
        $header = $this->teamMembership->client->token;
        $header['fileName'] = 'cat_pile.jpg';
        $this->post($this->fileUploadUri, $this->fileUploadInput, $header)
            ->seeStatusCode(403);
    }
}
