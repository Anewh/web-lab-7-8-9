<?php

namespace App\DataFixtures;


use App\Entity\User;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTime;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct (UserPasswordHasherInterface $passwordHasher) 
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // admin
        $user = new User();
        $user->setName('admin');
        $user->setEmail('admin@example.com');
        $user->setPhone(mt_rand(80000000000, 89999999999));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setApiToken('admin_api_token');
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                'admin_password'
            )
        );
        $manager->persist($user);

        $isActiveQuestionsCount = 5;

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName('user ' . $i);
            $user->setEmail('user' . $i . '@example.com');
            $user->setPhone(mt_rand(80000000000, 89999999999));
            $user->setRoles(['ROLE_USER']);
            $user->setApiToken('user' . $i . '_api_token');
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'user' . $i . 'password'
                )
            );
            $manager->persist($user);

            $question = new Question();
            $question->setHeader("header" . $i);
            $question->setText("Some test for question №" . $i);
            $question->setCategory("Pamagite");
            $question->setAuthor($user);

            $date = mt_rand(1641040050, 1643891250);
            $date = date("Y-m-d H:i:s", $date);
            $question->setAdded(DateTime::createFromFormat('Y-m-d H:i:s', $date));

            $question->setActive($isActiveQuestionsCount > 0);
            $isActiveQuestionsCount--;

            $manager->persist($question);

            $isActiveAnswersCount = 2;
            for ($j = 0; $j < 4; $j++) {
                $answer = new Answer();
                $answer->setText("Pamagau sebe sam " . $i);
                $answer->setAuthor($user);
                $answer->setQuestion($question);

                $date = mt_rand(1641040050, 1643891250);
                $date = date("Y-m-d H:i:s", $date);
                $answer->setAdded(DateTime::createFromFormat('Y-m-d H:i:s', $date));

                $answer->setActive($isActiveAnswersCount > 0);
                $isActiveAnswersCount--;

                $manager->persist($answer);
            }
        }
        $manager->flush();
    }
}
