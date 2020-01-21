<?php

// https://github.com/mikehaertl/php-shellcommand

use mikehaertl\shellcommand\Command;

require 'vendor/autoload.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

header("Cache-Control: post-check=0, pre-check=0", false);

header("Pragma: no-cache");

$params = $_REQUEST;

$path = $params["path"];

array_splice($params, 0, 1);

$vars = $params;

$method = $_SERVER['REQUEST_METHOD'];

$body = file_get_contents('php://input');

$endurl =  (count($vars) > 0) ? $path . '?' . http_build_query($vars) : $path;

$url = 'http:/APIVER' . $endurl;

$command = new Command(array('command' => 'curl'));

if (isset($params["streaming"])) :
    echo $command->nonBlockingMode = true;
endif;

$command->addArg('--unix-socket', '/var/run/docker.sock');

if ($method == 'POST') :
    $command->addArg('-X', 'POST');
endif;

$command->addArg($url);

if (!empty($body) && $method == 'POST') :
    $command->addArg('-H', '"Content-Type: application/json"');
    $command->addArg('-d', "'" . $body . "'");
endif;

if (isset($params["debug"])) :
    header('Content-Type:text/plain');
    echo $command->getExecCommand();
else :

    header("Content-type: application/json; charset=utf-8");

    if ($command->execute()) {
        $exitCode = $command->getExitCode();
    } else {
        $exitCode = $command->getExitCode();
    }

    if ($exitCode > 0) :
        http_response_code(500);
        echo json_encode(array("exit" => $exitCode,  "error" => $command->getError()));
    else :
        http_response_code(200);
        if (isset($params["array"])) :
            echo json_encode($command->getOutput());
        else :
            echo $command->getOutput();
        endif;

    endif;

endif;
