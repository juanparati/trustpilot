<?php

namespace Juanparati\Trustpilot\API;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Api
{
    /**
     * The Guzzle client.
     *
     * @var PendingRequest
     */
    private PendingRequest $client;

    /**
     * The trustpilot configuration.
     *
     * @var array
     */
    private mixed $config;

    /**
     * The api endpoint.
     *
     * @var string
     */
    private string $endpoint;

    /**
     * The rest object path.
     *
     * @var string
     */
    private string $path;

    /**
     * The access token cache key.
     *
     * @var string
     */
    private string $accessTokenKey;

    /**
     * The refresh token cache key.
     *
     * @var string
     */
    private string $refreshTokenKey;

    /**
     * Initialise the Api
     */
    public function __construct()
    {
        // Initalise the config
        $this->config = config('trustpilot');

        $this->path = '/';
        $this->endpoint = $this->config['endpoints']['default'];
        $this->accessTokenKey = $this->config['cache']['prefix'] . '.access_token';
        $this->refreshTokenKey = $this->config['cache']['prefix'] . '.refresh_token';

        $this->client = Http::withHeaders(['apikey' => $this->config['api']['key']]);
    }

    /**
     * Dynamically allow static calls of methods.
     *
     * @return self
     */
    public static function __callStatic($name, $arguments)
    {
        $class = get_called_class();
        $instance = new $class();
        return call_user_func_array([$instance, $name], $arguments);
    }

    /**
     * Set the endpoint of the api.
     *
     * @param string $endpoint
     * @return self
     */
    protected function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Set the path of the api.
     *
     * @param string $path
     * @return self
     */
    protected function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get the OAuth access token.
     *
     * @return string|null
     */
    protected function getAccessToken(): ?string
    {
        // Get from cache
        if (Cache::has($this->accessTokenKey)) {
            $accessToken = Cache::get($this->accessTokenKey);
            if ($accessToken != null) {
                return $accessToken;
            }
        }

        // Refresh if refresh token exists
        if (Cache::has($this->refreshTokenKey)) {
            $accessToken = $this->requestAccessToken('/refresh', 'refresh_token', [
                'refresh_token' => Cache::get($this->refreshTokenKey),
            ]);
            if ($accessToken != null) {
                return $accessToken;
            }
        }

        // Otherwise request a new token
        return $this->requestAccessToken('/accesstoken', 'password', [
            'username' => $this->config['credentials']['username'],
            'password' => $this->config['credentials']['password'],
        ]);
    }

    /**
     * Get the OAuth access token from the server.
     *
     * @param string $method
     * @param string $grantType
     * @param array $params
     * @return string|null
     */
    private function requestAccessToken($method, $grantType, $params): ?string
    {
        $params['grant_type'] = $grantType;

        $response = $this->client->send('POST', $this->config['endpoints']['oauth'] . $method, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->config['api']['key'] . ':' . $this->config['api']['secret']),
            ],
            'form_params' => $params,
        ]);

        if ($response->getStatusCode() != 200) {
            return null;
        }

        $result = json_decode((string) $response->getBody());

        // Cache the access token and refresh token
        Cache::put($this->accessTokenKey, $result->access_token, $result->expires_in / 1000);
        Cache::put($this->refreshTokenKey, $result->refresh_token, $result->refresh_token_expires_in / 1000);

        return $result->access_token;
    }

    /**
     * Perform a request to the API service.
     *
     * @param string $method
     * @param string $path
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return object|null
     * @throws \Exception
     */
    protected function request(string $method, string $path, array $query = [], array $params = [], bool $auth = false) : object
    {
        $body = [];

        // If auth is required, append access token
        if ($auth) {
            $body['headers'] = [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ];
        }

        if (!empty($query))
            $body['query'] = $query;

        // Some Truspilot endpoints will raise a 403 error in case that body is empty
        if (!empty($params))
            $body['json'] = $params;

        // Perform request
        $response = $this->client->send($method, $this->endpoint . $this->path . $path, $body);
        return $response->object();
    }

    /**
     * Perform a GET request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param bool $auth
     * @return object
     * @throws GuzzleException
     */
    protected function get(string $path, array $query = [], bool $auth = false)
    {
        return $this->request('GET', $path, $query, [], $auth);
    }

    /**
     * Perform a POST request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return object
     * @throws GuzzleException
     */
    protected function post(string $path, array $query = [], array $params = [], bool $auth = false)
    {
        return $this->request('POST', $path, $query, $params, $auth);
    }

    /**
     * Perform a PUT request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return object
     * @throws GuzzleException
     */
    protected function put(string $path, array $query = [], array $params = [], bool $auth = false)
    {
        return $this->request('PUT', $path, $query, $params, $auth);
    }

    /**
     * Perform a DELETE request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return object
     * @throws GuzzleException
     */
    protected function delete(string $path, array $query = [], array $params = [], bool $auth = false)
    {
        return $this->request('DELETE', $path, $query, $params, $auth);
    }
}
