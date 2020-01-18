<?php

class DockerManager
{

    const WORKING_DIR = '/var/www/html';

    private $ver;

    function __construct($_ver = 'v1.26')
    {
        $this->ver = $_ver;
    }


    public function make($method, $path, $args = array(), $json = null)
    {
        $vars = "";
        if (count($args) > 0) {
            $vars = '?' . http_build_query($args);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_UNIX_SOCKET_PATH, '/var/run/docker.sock');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        if ('POST' === $method) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        $url = "http://unixsocket{$this->ver}/{$path}{$vars}";
        echo $url;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        if ('POST' === $method) :
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        endif;
        try {
            $response = curl_exec($ch);
            echo $response;
            http_response_code(200);
        } catch (Exception $e) {
            echo $e->getMessage();
            http_response_code(500);
        }
        curl_close($ch);
    }
}

$params = $_REQUEST;

$path = '/' . array_keys($_REQUEST)[0];
echo $path;

array_splice($_REQUEST, 0, 1);

$vars = $_REQUEST;

$method = $_SERVER['REQUEST_METHOD'];
echo $method;

$body = file_get_contents('php://input');
echo $body;

$docker_manager = new DockerManager();

$docker_manager->make($method, $path, $vars, $body);
