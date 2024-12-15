<?php

namespace App\Service\Http;

use App\Exception\ExternalServiceHttpFail;
use App\Exception\InvalidResultException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TelegramHttpClient
{
    const BASE_URI_PATTERN = 'https://api.telegram.org/bot%d:%s/%s';

    private $client;

    private string $token;

    protected int $id;

    public function __construct(HttpClientInterface $client, int $telegramBotId, string $telegramBotToken)
    {
        $this->client = $client;
        $this->id = $telegramBotId;
        $this->token = $telegramBotToken;
    }

    public function getBotId() : int
    {
        return $this->id;
    }

    public function getMe() : ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->id, $this->token, 'getMe');

        $response = $this->client->request('GET', $url);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            //TODO: log event
            throw new HttpException($response->getStatusCode(), "Check client settings");
        }

        return $response;
    }

    public function getUpdates()
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->id, $this->token, 'getUpdates');

        $response = $this->client->request('GET', $url);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            //TODO: log event
            throw new HttpException($response->getStatusCode(), "Check client settings");
        }

        return $response;
    }

    public function getWebhookInfo()
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->id, $this->token, 'getWebhookInfo');

        $response = $this->client->request('GET', $url);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            //TODO: log event
            throw new HttpException($response->getStatusCode(), "Check client settings");
        }

        return $response;
    }

    public function setWebhook(string $baseUri) : ResponseInterface
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->id, $this->token, 'setWebhook');

        //TODO: get url from external params
        $body = [
            'url' => $baseUri . sprintf('bot%d:%s/%s', $this->id, $this->token, 'messages')
        ];

        $response = $this->client->request('POST', $url, [
            'body' => $body
        ]);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            //TODO: throw another type of exception
            throw new ExternalServiceHttpFail(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }

    public function sendMessage(int $chatId, string $text)
    {
        $url = sprintf(self::BASE_URI_PATTERN, $this->id, $this->token, 'sendMessage');

        $response = $this->client->request('POST', $url, [
            'body' => [
                'chat_id' => $chatId,
                'text' => $text
            ],
            'extra' => ['trace_content' => true]
        ]);

        if(Response::HTTP_OK !== $response->getStatusCode()) {
            //TODO: throw another type of exception
            throw new ExternalServiceHttpFail(sprintf('Bad code %d. on %s', $response->getStatusCode(), $url));
        }

        return $response;
    }

    public function responseToArray(ResponseInterface $response) : array
    {
        $result = json_decode($response->getContent(), true);

        if(true !== $result['ok']) {
            throw new InvalidResultException("ok should be true in result");
        }

        //TODO: check if array
        return $result['result'];
    }
}
