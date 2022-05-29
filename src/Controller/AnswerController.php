<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/answers')]
class AnswerController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_answer_index', methods: ['GET'])]
    public function index(Request $request, AnswerRepository $answerRepository): Response
    {
        if ($request->query->has('question-id')) {
            $answers = $answerRepository->findBy([
                'question' => $request->query->get('question-id'),
                'active' => true
            ]);
        } else {
            if ($this->isGranted('ROLE_ADMIN')) {
                $answers = $answerRepository->findAll();
            } else {
                return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->render('answer/index.html.twig', [
            'answers' => $answers,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/new', name: 'app_answer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AnswerRepository $answerRepository, QuestionRepository $questionRepository): Response
    {
        $answer = new Answer();

        $questionId = intval($request->query->get('question-id'));
        if ($questionId > 0) {
            $question = $questionRepository->find($questionId);
            $answer->setQuestion($question);
        }

        $form = $this->createForm(AnswerType::class, $answer, [
            'question-id' => $questionId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionId = intval($form->get('question-id')->getData());
            $question = $questionRepository->find($questionId);
            $answer->setQuestion($question);

            $answer->setAuthor($this->security->getUser());
            $answer->setAdded(new DateTime());
            $answer->setActive(false);
            $answerRepository->add($answer);

            return $this->redirectToQuestion($questionId);
        }

        return $this->renderForm('answer/new.html.twig', [
            'answer' => $answer,
            'form' => $form,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_answer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Answer $answer, AnswerRepository $answerRepository, QuestionRepository $questionRepository): Response
    {
        $questionId = intval($request->query->get('question-id'));
        if ($questionId > 0) {
            $question = $questionRepository->find($questionId);
            $answer->setQuestion($question);
        }
        $form = $this->createForm(AnswerType::class, $answer, [
            'question-id' => $questionId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionId = intval($form->get('question-id')->getData());
            $question = $questionRepository->find($questionId);
            $answer->setQuestion($question);

            $answerRepository->add($answer);
            return $this->redirectToQuestion($answer->getQuestion()->getId());
        }

        return $this->renderForm('answer/edit.html.twig', [
            'answer' => $answer,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_answer_delete', methods: ['POST'])]
    public function delete(Request $request, Answer $answer, AnswerRepository $answerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$answer->getId(), $request->request->get('_token'))) {
            $answerRepository->remove($answer);
        }
        return $this->redirectToQuestion($answer->getQuestion()->getId());
    }

    /**
     * @param int $questionId
     * @return RedirectResponse
     */
    private function redirectToQuestion(int $questionId): RedirectResponse
    {
        return $this->redirectToRoute('app_question_show', [
            'id' => $questionId
        ], Response::HTTP_SEE_OTHER);
    }
}
