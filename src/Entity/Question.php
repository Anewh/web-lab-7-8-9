<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['getById', 'getCollection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['getById', 'getCollection'])]
    private $header;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    #[Groups(['getById'])]
    private $text;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['getById', 'getCollection'])]
    private $category;

    #[ORM\Column(type: 'date')]
    #[Groups(['getById'])]
    private $added;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $author;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class, orphanRemoval: true)]
    #[Groups(['getById'])]
    private $answers;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['getById', 'getCollection'])]
    private $active;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(string $header): self
    {
        $this->header = $header;

        return $this;
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

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

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

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
