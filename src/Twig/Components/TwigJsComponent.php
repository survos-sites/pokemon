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
    public ?string $refreshEvent=null;

    public function getRefreshEvent(): string
    {
        return $this->refreshEvent;
    }

    public function getFilter(): null
    {
        return $this->filter;
    }

    public function getStore(): string
    {
        return $this->store;
    }
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
