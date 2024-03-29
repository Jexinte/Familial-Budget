<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Oops! Ce champ ne peut être vide !')]
    private ?string $name = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Oops! Veuillez sélectionner la catégorie de votre dépense !')]
    private string $category;

    #[ORM\Column]
    #[Assert\NotBlank(message:'Oops! Veuillez spécifier le montant de vôtre dépense !')]
    private ?float $amount = null;

//    #[ORM\Column(enumType: Priority::class)]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Oops! Merci de spécifier la priorité de vôtre dépense !')]
//    private ?Priority $priority = null;
    private ?string $priority = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'expense')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SpendingProfile $spendingProfile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = floatval($amount);

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getSpendingProfile(): ?SpendingProfile
    {
        return $this->spendingProfile;
    }

    public function setSpendingProfile(?SpendingProfile $spendingProfile): static
    {
        $this->spendingProfile = $spendingProfile;

        return $this;
    }
}
