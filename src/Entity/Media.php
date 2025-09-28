<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use App\Workflow\MediaFlow;
use Doctrine\ORM\Mapping as ORM;
use Survos\StateBundle\Traits\MarkingInterface;
use Survos\StateBundle\Traits\MarkingTrait;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media implements \Stringable, MarkingInterface
{
    use MarkingTrait;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column]
        private(set) ?string $id = null,
        #[ORM\Column]
        private(set) ?string $originalUrl = null,
    )
    {
        $this->marking = MediaFlow::PLACE_NEW;
    }

    public function __toString()
    {
        return $this->originalUrl;
    }
}
