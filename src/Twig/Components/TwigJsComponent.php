<?php

namespace App\Twig\Components;

use App\TwigBlocksTrait;
use Psr\Log\LoggerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Twig\Environment;

#[AsTwigComponent(name: 'dexie', template: 'components/dexie_twig.html.twig')]
final class TwigJsComponent
{
    use TwigBlocksTrait;
    public string $store; // required
    public $filter = null;
    public array $order = [];

    public function __construct(
        private Environment $twig,
        private LoggerInterface $logger,
    ) {

        //        ='@survos/grid-bundle/api_grid';
    }

    public function getTwigTemplate(): string
    {
        return $this->getTwigBlocks()['twig_template'];

    }


}
