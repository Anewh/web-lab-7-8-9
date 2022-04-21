<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
use DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 25; $i++) {
            $user = new User();
            $user->setName('user ' . $i);
            $user->setEmail('user' . $i . '@example.com');
            $user->setPhone(mt_rand(80000000000, 89999999999));
            $user->setHash(password_hash('user' . $i . 'password', PASSWORD_DEFAULT));
            $manager->persist($user);

            $question = new Question();
            $question->setHeader("header" . $i);
            $question->setText("Some test for question №" . $i);
            $question->setCategory("Pamagite");
            $question->setAuthor($user);
            $date = mt_rand(1641040050, 1643891250);
            $date = date("Y-m-d H:i:s", $date);
            $question->setAdded(DateTime::createFromFormat('Y-m-d H:i:s', $date));
            $manager->persist($question);

            $answer = new Answer();
            $answer->setText("Pamagau sebe sam");
            $answer->setAuthor($user);
            $answer->setQuestion($question);
            $date = mt_rand(1641040050, 1643891250);
            $date = date("Y-m-d H:i:s", $date);
            $answer->setAdded(DateTime::createFromFormat('Y-m-d H:i:s', $date));
            $manager->persist($answer);
        }
        $manager->flush();
    }
}
