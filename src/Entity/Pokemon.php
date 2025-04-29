<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PokemonRepository;
use App\Workflow\IPokemonWorkflow;
use Doctrine\ORM\Mapping as ORM;
use Survos\WorkflowBundle\Traits\MarkingInterface;
use Survos\WorkflowBundle\Traits\MarkingTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ORM\Index(name: 'pokemon_marking', columns: ['marking'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    shortName: 'pokemon',
    paginationItemsPerPage: 5,
    normalizationContext: ['groups' => ['pokemon.read', 'marking']],
)]
#[ApiFilter(SearchFilter::class, properties: ['marking' => 'exact'])]
#[Groups(['pokemon.read'])]
class Pokemon implements MarkingInterface
{
    use MarkingTrait;

    const BASE_URL = 'https://pokeapi.co/api/v2/pokemon/';

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private array $details = [];

    #[ORM\Column(nullable: true)]
    private ?bool $owned = null;

    #[ORM\Column(nullable: true)]
    public ?int $fetchStatusCode = null;

    #[ORM\Column(nullable: true)]
    private ?int $downloadStatusCode = null;

    /**
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column]
        private int $id
    )
    {
        $this->marking = IPokemonWorkflow::PLACE_NEW;
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
        return self::BASE_URL . sprintf('/img/%d.png', $this->getId());
    }

    public function getDetailUrl(): ?string
    {
        // we may refactor this someday
        return self::BASE_URL . $this->getId();
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

    public function getFetchStatusCode(): ?int
    {
        return $this->fetchStatusCode;
    }

    public function setFetchStatusCode(?int $fetchStatusCode): static
    {
        $this->fetchStatusCode = $fetchStatusCode;

        return $this;
    }

    public function getDownloadStatusCode(): ?int
    {
        return $this->downloadStatusCode;
    }

    public function setDownloadStatusCode(?int $downloadStatusCode): static
    {
        $this->downloadStatusCode = $downloadStatusCode;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->details['sprites']['front_default'] ?? null;
    }
}
