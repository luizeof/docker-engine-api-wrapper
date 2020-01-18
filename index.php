<?php

use mikehaertl\shellcommand\Command;

require 'vendor/autoload.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/json; charset=utf-8");

$params = $_REQUEST;

$path = $_REQUEST["path"];

array_splice($_REQUEST, 0, 1);

$vars = $_REQUEST;

$method = $_SERVER['REQUEST_METHOD'];

$body = file_get_contents('php://input');

$url =  (count($vars) > 0) ? $path . '?' . http_build_query($vars) : $path;

$url = 'http:/APIVER' . $url;

$command = new Command(array(
    'command' => '/usr/bin/curl'
));

// Add arguments with correct escaping:
// results in --name='d'\''Artagnan'
$command->addArg('--unix-socket', '/var/run/docker.sock');

if ($method == 'POST') :
    $command->addArg('-X', 'POST');
endif;

$command->addArg($url);

// Add argument with several values
// results in --keys key1 key2
$command->addArg('-H', '"Content-Type: application/json"');

$command->addArg('-d', $body);

if ($command->execute()) {
    echo $command->getOutput();
} else {
    echo $command->getError();
    $exitCode = $command->getExitCode();
}
