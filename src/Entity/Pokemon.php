<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ApiResource(
    shortName: 'pokemon',
    paginationItemsPerPage: 10,
    normalizationContext: ['groups' => ['pokemon.read']],
)]
#[Groups(['pokemon.read'])]
class Pokemon
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private array $details = [];

    #[ORM\Column(nullable: true)]
    private ?bool $owned = null;

    /**
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column]
        private int $id
    )
    {
    }

    public function getId(): int
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

    #[Groups(['pokemon.read'])]
    public function getImageUrl(): ?string
    {
        // we may refactor this someday
        return sprintf('img/%d.png', $this->getId());
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function isOwned(): ?bool
    {
        return $this->owned;
    }

    public function setOwned(?bool $owned): static
    {
        $this->owned = $owned;

        return $this;
    }
}
