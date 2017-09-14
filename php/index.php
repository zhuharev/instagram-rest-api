<?php

//set_time_limit(0);
//date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

use \InstagramAPI\Constants;

$username = $_POST["login"];
$password = $_POST["password"];
$captionText = $_POST["caption"];

$files_count = count($_FILES["files"]["tmp_name"]);
$tmpFiles = $_FILES["files"]["tmp_name"];
$media = makeMedia($tmpFiles);
$files_count = count($media);

// Albums can contain between 2 and 10 photos/videos.

$debug = false;
$truncatedDebug = false;

function makeMedia($filePaths) {
  $media = [];
  for ($i=0; $i < count($filePaths); $i++) {
    if ($filePaths[$i] == "") {
      continue;
    }
    $media[] = [
      'type' => 'photo',
      'file' => $filePaths[$i],
    ];
  }
  return $media;
}

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug,[
    'storage'    => 'file',
    'basefolder' => '/var/www/html/sessions',
]);

try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo json_encode(['error'=>'Something went wrong: '.$e->getMessage()],false);
    exit(0);
}

if ($files_count == 1) {
  try {
      $resp = $ig->timeline->uploadPhoto($tmpFiles[0], ['caption' => $captionText]);
      echo json_encode($resp,false);
      exit(0);
  } catch (\Exception $e) {
      echo json_encode(['error'=>'Something went wrong: '.$e->getMessage()],false);
      exit(0);
  }
}else {
  try {
      $resp = $ig->timeline->uploadAlbum(makeMedia($tmpFiles), ['caption' => $captionText]);
      echo json_encode($resp,false);
      exit(0);
  } catch (\Exception $e) {
      echo json_encode(['error'=>'Something went wrong: '.$e->getMessage()],false);
      exit(0);
  }
}



echo json_encode(['response'=>'ok'],false);
