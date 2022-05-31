<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['getCollection']],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['getById']],
        ],
    ],
//    attributes: ["security" => "is_granted('ROLE_USER')"],
    normalizationContext: ['groups' => ['getCollection', 'getById']],
)]
#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['getById', 'getCollection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 1024)]
    #[Groups(['getById'])]
    private $text;

    #[ORM\Column(type: 'date')]
    #[Groups(['getById'])]
    private $added;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $author;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private $question;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['getById', 'getCollection'])]
    private $active;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
