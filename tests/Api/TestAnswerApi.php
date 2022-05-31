<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Answer;
use App\Entity\User;
use App\Repository\AnswerRepository;
use App\Tests\TestUtils;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

class TestAnswerApi extends ApiTestCase
{
    public function testFindOneAnswer()
    {
        $client = static::createClient();
        $em = static::getContainer()->get('doctrine')->getManager();
        $answerRepository = $em->getRepository(Answer::class);
        $response = $client->request('GET', '/api/answers', [
            'query' => ['page' => 1],  'headers' => [ 'X-AUTH-TOKEN' => 'user1_api_token']
        ]);

        $id = $response->toArray()['hydra:member'][0]['id'];
        $client->request('GET', '/api/answers/' . $id);
        self::assertResponseStatusCodeSame(401);
        $client->request('GET', '/api/answers/' . $id, [
            'headers' => ['X-AUTH-TOKEN' => 'user1_api_token']
        ]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $answer = $answerRepository->find($id);
        $author = $answer->getAuthor();

        self::assertJsonContains([
            "@context" => "/api/contexts/Answer",
            "@id" => '/api/answers/' . $id,
            "@type" => "Answer",
            "id" => $id,
            "added" => $answer->getDateCreated()->format('Y-m-d\TH:i:sP'),
            "text" => $answer->getText(),
            "active" => $answer->getActive(),
            "author" => [
                "@type" => "User",
                "id" => $author->getId(),
                "name" => $author->getName()
            ]
        ]);
        $this->assertMatchesResourceItemJsonSchema(Answer::class);
    }

    public function testFindAnswers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/answers', [
            'query' => ['page' => 1]
        ]);
        self::assertResponseStatusCodeSame(401);
        $client->request('GET', '/api/answers', [
            'query' => ['page' => 1],  'headers' => [ 'X-AUTH-TOKEN' => 'stupid_token']
        ]);
        self::assertResponseStatusCodeSame(401);
        $client->request('GET', '/api/answers', [
            'query' => ['page' => 1],  'headers' => [ 'X-AUTH-TOKEN' => 'user1_api_token']
        ]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            "@context" => "/api/contexts/Answer",
            "@id" => '/api/answers',
            "@type" => "hydra:Collection",
            'hydra:totalItems' => 40,
        ]);
        $this->assertMatchesResourceCollectionJsonSchema(Answer::class);
    }
}
