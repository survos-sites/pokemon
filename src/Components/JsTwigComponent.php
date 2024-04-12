<?php

//namespace Survos\ApiGrid\Components;
namespace App\Components;

use App\TwigBlocksTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Twig\Environment;

#[AsTwigComponent('jsTwig', template: '@SurvosApiGrid/components/js_twig.html.twig')]
class JsTwigComponent // implements TwigBlocksInterface
{
    use TwigBlocksTrait;

    public string $caller;
    public string $apiUrl;
    public string $id; // for parsing out the twig blocks
    public function __construct(
        private Environment $twig,
        private LoggerInterface $logger,
    ) {

        //        ='@survos/grid-bundle/api_grid';
    }


}
