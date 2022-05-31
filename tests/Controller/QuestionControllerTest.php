<?php

namespace App\Tests\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Tests\AbstractTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

class QuestionControllerTest extends WebTestCase
{
    public function testIndexShow()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        self::assertResponseRedirects();
        $crawler = $client->followRedirect();
        self::assertResponseStatusCodeSame(200);
        self::assertRouteSame('app_question_index');
        self::assertEquals(6, $crawler->filter('tr')->count());

        $crawler = $client->clickLink('header0');
        self::assertResponseStatusCodeSame(200);
        self::assertEquals(2, $crawler->filter('.border-bottom')->count());
    }

    public function testNewQuestion()
    {
        $client = static::createClient();
        $client->followRedirects();

        $questionRepository = static::getContainer()->get('doctrine')->getManager()->getRepository(Question::class);
        $notConfirmedQuestions = $questionRepository->count(['active' => false]);
        $client->request('GET', '/');
        $crawler = $client->clickLink('Задать вопрос');
        self::assertResponseStatusCodeSame(200);

        $form = $crawler->filter('form')->first()->form();
        $form['email'] = 'user1@example.com';
        $form['password'] = 'user1password';
        $crawler = $client->submit($form);

        $form = $crawler->filter('form')->first()->form();
        $form['question[header]'] = '';
        $form['question[category]'] = '';
        $form['question[text]'] = '';
        $client->submit($form);
        self::assertResponseStatusCodeSame(500);

        $form['question[header]'] = 'Question';
        $form['question[category]'] = 'Category';
        $form['question[text]'] = 'Description';
        $client->submit($form);
        self::assertResponseStatusCodeSame(200);

        self::assertEquals(
            $notConfirmedQuestions + 1,
            $questionRepository->count(['active' => false])
        );
    }
}
