<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HackerNewsClient
{
    private $client;
    private $baseUri;

    public function __construct(GuzzleClient $client, $baseUri = null)
    {
        /**
         * @var GuzzleClient $client
         */
        $this->client = $client;
        $this->baseUri = $baseUri;
    }

    /**
     * @param $url
     * @return string
     */
    public function buildUrl($url){
        return "{$this->baseUri}/{$url}.json";
    }

    /**
     * @param $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($url)
    {
        try {
            $response = $this->client->request("GET", $this->buildUrl($url));

            if($response->getStatusCode() !== 200){
                throw new \Exception(
                    "HackerNews get request has failed to retrieve any results");
            }

            return json_decode($response->getBody(), true);

        }catch(\Throwable $e){
            throw new NotFoundHttpException($e->getMessage(), 404);
        }
    }

    /**
     * @param array $ids
     * @return array
     * Get items async for a smoother performance
     */
    public function getItemsAsync(array $ids = [])
    {
        $results = [];

        try {
            foreach ($ids as $key => $id) {
                $promise = $this->client->requestAsync("GET", $this->buildUrl("item/{$id}"));
                $response = $promise->wait();
                /**
                 * Iterate to each response and make sure we get the ones who have succeeded by checking the status code
                 * @var Response $response
                 */
                if ($response->getStatusCode() === 200) {
                    $results[] = json_decode($response->getBody(), true);
                }
            }

            return $results;
        }catch(\Throwable $e){
            throw new NotFoundHttpException($e->getMessage(), 404);
        }
    }
}