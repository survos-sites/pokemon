<?php

namespace App\Twig\Components;

use App\TwigBlocksTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Twig\Environment;

#[AsTwigComponent(template: 'components/DetailRender.html.twig')]
final class DetailRender
{
    use TwigBlocksTrait;

    public function __construct(
        private Environment $twig,
    ) {

    }

    public array $payload;
    public string $caller; // _self, there's gotta be a way to get rid of this!

    public function getBlocks(): array
    {
        $blocks = $this->getTwigBlocks();
        return $blocks;
    }

    public function getTwigTemplate(): string
    {
        return "<i>xx</i>";
    }
}
