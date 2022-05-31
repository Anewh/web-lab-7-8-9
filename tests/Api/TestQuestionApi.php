<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Question;
use App\Entity\User;
use App\Repository\QuestionRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;

class TestQuestionApi extends ApiTestCase
{
    public function testFindOneQuestion()
    {
        $client = static::createClient();
        $em = static::getContainer()->get('doctrine')->getManager();
        $questionRepository = $em->getRepository(Question::class);
        $response = $client->request('GET', '/api/questions', [
            'query' => ['page' => 1],  'headers' => [ 'X-AUTH-TOKEN' => 'user1_api_token']
        ]);

        $id = $response->toArray()['hydra:member'][0]['id'];
        $client->request('GET', '/api/questions/' . $id);
        self::assertResponseStatusCodeSame(401);
        $client->request('GET', '/api/questions/' . $id, [
            'headers' => [ 'X-AUTH-TOKEN' => 'user1_api_token']
        ]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $question = $questionRepository->find($id);
        $author = $question->getAuthor();

        self::assertJsonContains([
            "@context" => "/api/contexts/Question",
            "@id" => '/api/questions/' . $id,
            "@type" => "Question",
            "id" => $id,
            "header" => $question->getHeader(),
            "text" => $question->getText(),
            "added" => $question->getDateCreated()->format('Y-m-d\TH:i:sP'),
            "active" => $question->getActive(),
            "author" => [
                "@type" => "User",
                "id" => $author->getId(),
                "name" => $author->getName()
            ]
        ]);
        $this->assertMatchesResourceItemJsonSchema(Question::class);
    }

    public function testFindQuestions()
    {
        $client = static::createClient();
        $client->request('GET', '/api/questions', [
            'query' => ['page' => 1]
        ]);
        self::assertResponseStatusCodeSame(401);
        $client->request('GET', '/api/questions', [
            'query' => ['page' => 1],  'headers' => [ 'X-AUTH-TOKEN' => 'user1_api_token']
        ]);
        self::assertResponseStatusCodeSame(200);

        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            "@context" => "/api/contexts/Question",
            "@id" => '/api/questions',
            "@type" => "hydra:Collection",
            'hydra:totalItems' => 10,
        ]);
        $this->assertMatchesResourceCollectionJsonSchema(Question::class);
    }
}
