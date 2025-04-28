<?php

namespace App\Workflow;

use App\Entity\Pokemon;
use Survos\SaisBundle\Model\ProcessPayload;
use Survos\SaisBundle\Service\SaisClientService;
use Survos\WorkflowBundle\Attribute\Workflow;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Workflow(supports: [Pokemon::class], name: self::WORKFLOW_NAME)]
class PokemonWorkflow implements IPokemonWorkflow
{
    public const WORKFLOW_NAME = 'PokemonWorkflow';

    public function __construct(
        private HttpClientInterface                              $httpClient,
        private SaisClientService                                $saisClientService,
        #[Target(self::WORKFLOW_NAME)] private WorkflowInterface $workflow,
        #[Autowire('%env(SAIS_ROOT)%')] private string $rootDir,
    )
    {
    }


    public function getPokemon(TransitionEvent|GuardEvent|CompletedEvent $event): Pokemon
    {
        /** @var Pokemon */
        return $event->getSubject();
    }

    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH)]
    public function onFetch(TransitionEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
        $url = $pokemon->getDetailUrl();
        $request = $this->httpClient->request('GET', $url);
        $statusCode = $request->getStatusCode();
        $pokemon
            ->setFetchStatusCode($statusCode);
        if ($statusCode === 200) {
            $details = json_decode($request->getContent(), true);
            $pokemon->setDetails($details);
        }
    }

    #[AsCompletedListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH)]
    public function asFetchCompleted(CompletedEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
        if ($pokemon->getFetchStatusCode() !== 200) {
            $this->workflow->apply($pokemon, self::TRANSITION_FAIL_FETCH);
        }
    }


    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_DOWNLOAD)]
    public function onDownload(TransitionEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
//        $image = $this->rootDir . $pokemon->getImageUrl();
        $imageUrl = sprintf('https://raw.githubusercontent.com/HybridShivam/Pokemon/master/assets/images/%03d.png', $pokemon->getId());
        $response = $this->saisClientService->dispatchProcess(new ProcessPayload(
             $this->rootDir,
            [$imageUrl],
            // @todo: resize callback,
        ));
        dump($response);
    }


}
