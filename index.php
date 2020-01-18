<?php

class DockerManager
{
    const DOCKER_SOCKET = '/var/run/docker.sock';

    const WORKING_DIR = '/var/www/html';

    private $ver;

    function __construct($_ver = 'v1.26')
    {
        $this->ver = $_ver;
    }

    private function curl_prepare(&$ch, $method = 'POST')
    {
        curl_setopt($ch, CURLOPT_UNIX_SOCKET_PATH, static::DOCKER_SOCKET);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        if ('POST' === $method) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    }

    public function make($method, $path, $args = array(), $json = null)
    {
        $vars = "";
        if (count($args) > 0) {
            $vars = '?' . http_build_query($args);
        }

        $ch = curl_init();
        $this->curl_prepare($ch, $method);
        curl_setopt($ch, CURLOPT_URL, "http:/{$this->ver}/{$path}{$vars}");
        if ('POST' === $method) :
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        endif;
        try {
            $response = json_decode(curl_exec($ch));
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

$path = array_keys($_REQUEST)[0];

array_splice($_REQUEST, 0, 1);

$vars = $_REQUEST;

$method = $_SERVER['REQUEST_METHOD'];

$body = file_get_contents('php://input');

$docker_manager = new DockerManager();

$docker_manager->make($method, $path, $vars, $body);
