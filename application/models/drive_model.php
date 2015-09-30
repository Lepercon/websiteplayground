<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Drive_model extends CI_Model {

    function Drive_model() {
        parent::__construct();
    }
    
    function auth(){
        include APPPATH.'/libraries/drive/src/Google/autoload.php';
        $key = file_get_contents(APPPATH.'/libraries/drive/src/Google/API.p12');
        $iss = '42698940667-6o7te31c4nhe7cqm4kt7qcrko5fp2kga@developer.gserviceaccount.com';
        $scope = 'https://www.googleapis.com/auth/drive';
          $auth = new Google_Auth_AssertionCredentials($iss, array($scope), $key);
          $client = new Google_Client();
          $client->setAssertionCredentials($auth);
          $drive = new Google_Service_Drive($client);
          if ($client->getAuth()->isAccessTokenExpired()) {
          $client->getAuth()->refreshTokenWithAssertion($auth);
        }
        $_SESSION['token'] = $client->getAccessToken();
        $_SESSION['drive-auth'] = $drive;
    }
    
    function listFiles(){
        
        if(!isset($_SESSION['drive-auth'])){
            $this->auth();
        }
        $drive = $_SESSION['drive-auth'];
        $result = array();
        $pageToken = NULL;
        
        do {
            try {
                $parameters = array();
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                $files = $drive->files->listFiles($parameters);
        
                $result = array_merge($result, $files->getItems());
                $pageToken = $files->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = NULL;
            }
        } while ($pageToken);
        return $result;
    }

    function insertFile($title, $description, $parentId, $mimeType, $filename) {
        if(!isset($_SESSION['drive-auth'])){
            $this->auth();
        }
        $service = $_SESSION['drive-auth'];
        $file = new Google_Service_Drive_DriveFile();
        $file->setTitle($title);
        $file->setDescription($description);
        $file->setMimeType($mimeType);
        
        // Set the parent folder.
        if ($parentId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
        }
        
        try {
            $data = file_get_contents($filename);
            
            $createdFile = $service->files->insert($file, array(
                'data' => $data,
                'mimeType' => $mimeType,
                'uploadType' => 'media'
            ));
            
            $this->insertPermission($createdFile->getId(), '', 'anyone', 'reader');
            
            return $createdFile;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }
    
    function insertPermission($fileId, $value, $type, $role) {
        if(!isset($_SESSION['drive-auth'])){
            $this->auth();
        }
        $service = $_SESSION['drive-auth'];
          $newPermission = new Google_Service_Drive_Permission();
        $newPermission->setValue($value);
        $newPermission->setType($type);
        $newPermission->setRole($role);
        $newPermission->setWithLink(true);
        try {
            return $service->permissions->insert($fileId, $newPermission);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
        return NULL;
    }
    
    function deleteFile($fileId) {
        if(!isset($_SESSION['drive-auth'])){
            $this->auth();
        }
        $service = $_SESSION['drive-auth'];
          try {
            $service->files->delete($fileId);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }

}

