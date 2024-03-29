<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Oops! Ce champ ne peut être vide !')]
    #[Assert\Regex(
        pattern: '/^[A-Z][a-zA-Z0-9]{0,9}$/',
        message: 'Le nom utilisateur doit commencer par une lettre majuscule',
        match: true
    )]
    private ?string $username = null;

    /**
     * @var array<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Oops! Ce champ ne peut être vide !')]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SpendingProfile::class)]
    private Collection $spendingProfile;

    public function __construct()
    {
        $this->spendingProfile = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     * @return $this
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @return Collection<int, SpendingProfile>
     */
    public function getSpendingProfile(): Collection
    {
        return $this->spendingProfile;
    }

    public function addSpendingProfile(SpendingProfile $spendingProfile): static
    {
        if (!$this->spendingProfile->contains($spendingProfile)) {
            $this->spendingProfile->add($spendingProfile);
            $spendingProfile->setUser($this);
        }

        return $this;
    }

    public function removeSpendingProfile(SpendingProfile $spendingProfile): static
    {
        if ($this->spendingProfile->removeElement($spendingProfile)) {
            // set the owning side to null (unless already changed)
            if ($spendingProfile->getUser() === $this) {
                $spendingProfile->setUser(null);
            }
        }

        return $this;
    }
}
