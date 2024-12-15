<?php

namespace App\Tests\Service\Http;

use App\Service\Http\TelegramHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TelegramHttpClientTest extends TestCase
{
    public function testGetMeSuccess() : void
    {
        //TODO: implement 401 and 404 exceptions
        $responses = [
            new MockResponse('{
  "ok": true,
  "result": {
    "id": 1234,
    "is_bot": true,
    "first_name": "HTTP",
    "username": "bot",
    "can_join_groups": true,
    "can_read_all_group_messages": false,
    "supports_inline_queries": false
  }
}',  ['http_code' => 200]),
            new MockResponse('{
  "ok": false,
  "error_code": 401,
  "description": "Unauthorized"
}',  ['http_code' => 401]),
            new MockResponse('{
  "ok": false,
  "error_code": 404,
  "description": "Not Found"
}',  ['http_code' => 404]),
        ];

        $httpClient = new MockHttpClient($responses);
        $client = new TelegramHttpClient($httpClient, 1, 'token');

        $result = $client->getMe();
        $this->assertInstanceOf(ResponseInterface::class, $result);

        $body = json_decode($result->getContent(), true);
        $this->assertTrue($body['ok']);
    }

    public function testGetUpdatesSuccess() : void
    {
        $responses = [
            new MockResponse('{
  "ok": true,
  "result": [
    {
      "update_id": 464150753,
      "message": {
        "message_id": 179,
        "from": {
          "id": 63913496,
          "is_bot": false,
          "first_name": "Roman",
          "last_name": "Volhelmut",
          "username": "volhelmut",
          "language_code": "uk"
        },
        "chat": {
          "id": 63913496,
          "first_name": "Roman",
          "last_name": "Volhelmut",
          "username": "volhelmut",
          "type": "private"
        },
        "date": 1675547561,
        "text": "Yyy"
      }
    },
    {
      "update_id": 464150754,
      "message": {
        "message_id": 180,
        "from": {
          "id": 63913496,
          "is_bot": false,
          "first_name": "Roman",
          "last_name": "Volhelmut",
          "username": "volhelmut",
          "language_code": "uk"
        },
        "chat": {
          "id": 63913496,
          "first_name": "Roman",
          "last_name": "Volhelmut",
          "username": "volhelmut",
          "type": "private"
        },
        "date": 1675547665,
        "text": "/start",
        "entities": [
          {
            "offset": 0,
            "length": 6,
            "type": "bot_command"
          }
        ]
      }
    }
  ]
}'),
        ];

        $httpClient = new MockHttpClient($responses);
        $client = new TelegramHttpClient($httpClient, 1, 'token');

        $result = $client->getUpdates();

        $this->assertInstanceOf(ResponseInterface::class, $result);

    }

    public function testSomething() : void
    {
        $responses = [
            new MockResponse(''),
        ];

        $httpClient = new MockHttpClient($responses);
        $client = new TelegramHttpClient($httpClient, 1, 'token');

        $result = $client->setWebhook('http://localhost/');

        $this->assertInstanceOf(ResponseInterface::class, $result);

    }
}
