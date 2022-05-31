<?php

namespace App\Tests\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Tests\AbstractTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

class AnswerControllerTest extends WebTestCase
{
    public function testNewAnswer()
    {
        $client = static::createClient();
        $client->followRedirects();

        $answerRepository = static::getContainer()->get('doctrine')->getManager()->getRepository(Answer::class);
        $notConfirmedAnswers = $answerRepository->count(['active' => false]);

        $client->request('GET', '/');

        $crawler = $client->clickLink('Login');
        $form = $crawler->filter('form')->first()->form();
        $form['email'] = 'user1@example.com';
        $form['password'] = 'user1password';
        $client->submit($form);

        $crawler = $client->clickLink('header0');
        $form = $crawler->filter('form')->first()->form();

        $form['answer[text]'] = '';
        $client->submit($form);
        self::assertResponseStatusCodeSame(500);

        $form['answer[text]'] = 'New answer';
        $client->submit($form);
        self::assertResponseStatusCodeSame(200);
        self::assertRouteSame('app_question_show');
        self::assertEquals($notConfirmedAnswers + 1,$answerRepository->count(['active' => false]));
    }
}
