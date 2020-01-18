<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/json; charset=utf-8");

$params = $_REQUEST;

$path = array_keys($_REQUEST)[0];

array_splice($_REQUEST, 0, 1);

$vars = $_REQUEST;

$method = $_SERVER['REQUEST_METHOD'];

$body = file_get_contents('php://input');

$url =  (count($vars) > 0) ? $path . http_build_query($vars) : $path;

//echo $url;

$cmd = 'curl -s --unix-socket /var/run/docker.sock ' . '/APIVER/' . $url;

echo $cmd;

echo shell_exec($cmd);
