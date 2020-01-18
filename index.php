<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

class DockerClient
{

    /** @param resource */
    private $curlClient;

    /** @param string */
    private $socketPath;

    /** @param string|null */
    private $curlError = null;

    /**
     * Constructor: Initialises the Curl Resource, making it usable for subsequent
     *  API requests.
     *
     * @param string
     */
    public function __construct(string $socketPath)
    {
        $this->curlClient = curl_init();
        $this->socketPath = $socketPath;

        curl_setopt($this->curlClient, CURLOPT_UNIX_SOCKET_PATH, $socketPath);
        curl_setopt($this->curlClient, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Deconstructor: Ensure the Curl Resource is correctly closed.
     */
    public function __destruct()
    {
        curl_close($this->curlClient);
    }

    private function generateRequestUri(string $requestPath)
    {
        /* Please note that Curl doesn't use http+unix:// or any other mechanism for
         *  specifying Unix Sockets; once the CURLOPT_UNIX_SOCKET_PATH option is set,
         *  Curl will simply ignore the domain of the request. Hence why this works,
         *  despite looking as though it should attempt to connect to a host found at
         *  the domain "unixsocket". See L14 where this is set.
         *
         *  @see Client.php:L14
         *  @see https://github.com/curl/curl/issues/1338
         */
        return sprintf("http:/localhost/%s", $requestPath);
    }


    /**
     * Dispatches a command - via Curl - to Commander's Unix Socket.
     *
     * @param  string Docker Engine endpoint to hit.
     * @param  array  Data to post to $endpoint.
     * @return array  JSON decoded response from Commander.
     */
    public function dispatchCommand(string $method, string $endpoint,  $parameters = null): array
    {

        echo $this->generateRequestUri($endpoint);

        curl_setopt($this->curlClient, CURLOPT_URL, $this->generateRequestUri($endpoint));

        if ($method == 'POST') {
            $payload = ($parameters);
            curl_setopt($this->curlClient, CURLOPT_POSTFIELDS, $payload);
        }

        $writeFunction = function ($ch, $string) {
            echo $string;
            $length = strlen($string);
            printf("Received %d byte\n", $length);
            flush();
            return $length;
        };
        curl_setopt($this->curlClient, CURLOPT_WRITEFUNCTION, $writeFunction);

        $result = curl_exec($this->curlClient);

        if ($result === FALSE) {
            $this->curlError = curl_error($this->curlClient);
            return array();
        }

        echo $result;
    }


    /**
     * Returns a human readable string from Curl in the event of an error.
     *
     * @return bool|string
     */
    public function getCurlError()
    {
        return is_null($this->curlError) ? false : $this->curlError;
    }
}


$params = $_REQUEST;

$path = array_keys($_REQUEST)[0];

array_splice($_REQUEST, 0, 1);

$vars = $_REQUEST;

$method = $_SERVER['REQUEST_METHOD'];

$body = file_get_contents('php://input');

$client = new DockerClient('unix:///var/run/docker.sock');

$url = (count($vars) > 0) ? $path . http_build_query($vars) : $path;

echo $url;

$client->dispatchCommand($method, $url, $body);
