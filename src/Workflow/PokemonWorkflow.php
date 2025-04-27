<?php

namespace App\Workflow;

use App\Entity\Pokemon;
use Survos\WorkflowBundle\Attribute\Workflow;
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
        private HttpClientInterface $httpClient,
        #[Target(self::WORKFLOW_NAME)] private WorkflowInterface $workflow,
    )
	{
	}


	public function getPokemon(TransitionEvent|GuardEvent|CompletedEvent $event): Pokemon
	{
		/** @var Pokemon */ return $event->getSubject();
	}


	#[AsGuardListener(self::WORKFLOW_NAME)]
	public function onGuard(GuardEvent $event): void
	{
		$pokemon = $this->getPokemon($event);

		switch ($event->getTransition()->getName()) {
		/*
		e.g.
		if ($event->getSubject()->cannotTransition()) {
		  $event->setBlocked(true, "reason");
		}
		App\Entity\Pokemon
		*/
		    case self::TRANSITION_FETCH:
		        break;
		    case self::TRANSITION_DOWNLOAD:
		        break;
		    case self::TRANSITION_FAIL_FETCH:
		        break;
		    case self::TRANSITION_FAIL_DOWNLOAD:
		        break;
		}
	}


	#[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH)]
	public function onFetch(TransitionEvent $event): void
	{
		$pokemon = $this->getPokemon($event);
        $url = $pokemon->getDetailUrl();
        // hack to force fail
//        if ($pokemon->isOwned())
//            $url .= 'xxx';
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
            dd($pokemon->getMarking());
        }
        dump($pokemon->getMarking());

    }


	#[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_DOWNLOAD)]
	public function onDownload(TransitionEvent $event): void
	{
		$pokemon = $this->getPokemon($event);
	}


	#[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FAIL_FETCH)]
	public function onFailFetch(TransitionEvent $event): void
	{
		$pokemon = $this->getPokemon($event);
	}


	#[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FAIL_DOWNLOAD)]
	public function onFailDownload(TransitionEvent $event): void
	{
		$pokemon = $this->getPokemon($event);
	}
}
