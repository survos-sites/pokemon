<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PokemonRepository;
use App\Workflow\IPokemonWorkflow;
use Doctrine\ORM\Mapping as ORM;
use Survos\MeiliBundle\Api\Filter\FacetsFieldSearchFilter;
use Survos\MeiliBundle\Metadata\MeiliIndex;
use Survos\StateBundle\Traits\MarkingInterface;
use Survos\StateBundle\Traits\MarkingTrait;
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
#[MeiliIndex(
    filterable: self::FACETS,
    sortable: self::SORT_PROPERTIES,
)]
#[ApiFilter(FacetsFieldSearchFilter::class, properties: self::FACETS)]
#[ApiFilter(OrderFilter::class, properties: self::SORT_PROPERTIES)]

class Pokemon implements MarkingInterface, \Stringable
{
    public const array SORT_PROPERTIES = ['objCount','imgCount','listingObjectCount'];
    public const array FACETS = ['marking'];
    use MarkingTrait;

    const BASE_URL = 'https://pokeapi.co/api/v2/pokemon/';


    #[ORM\Column]
    #[Groups(['pokemon.read'])]
    public array $details = [];

    #[ORM\Column(nullable: true)]
    #[Groups(['pokemon.read'])]
    public array $resized = [];

    #[ORM\Column(nullable: true)]
    #[Groups(['pokemon.read'])]
    private ?bool $owned = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pokemon.read'])]
    public ?int $fetchStatusCode = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pokemon.read'])]
    public ?string $mediaCode = null; // points to media table.  Primary image, not (yet) audio, etc.

    /**
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column]
        #[Groups(['pokemon.read'])]
        private(set) int $id,

        #[ORM\Column(length: 255)]
        #[Groups(['pokemon.read'])]
        private(set) ?string $name = null
)
    {
        $this->marking = IPokemonWorkflow::PLACE_NEW;
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
        return self::BASE_URL . sprintf('/img/%d.png', $this->id);
    }

    public function getDetailUrl(): ?string
    {
        // we may refactor this someday
        return self::BASE_URL . $this->id;
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


    public function setFetchStatusCode(?int $fetchStatusCode): static
    {
        $this->fetchStatusCode = $fetchStatusCode;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->details['sprites']['front_default'] ?? null;
    }

    public function __toString()
    {
        return $this->name;
    }
}
