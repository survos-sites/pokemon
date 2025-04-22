<?php

namespace App\Workflow;

use App\Entity\Pokemon;
use Survos\WorkflowBundle\Attribute\Workflow;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

#[Workflow(supports: [Pokemon::class], name: self::WORKFLOW_NAME)]
class PokemonWorkflow implements IPokemonWorkflow
{
	public const WORKFLOW_NAME = 'PokemonWorkflow';

	public function __construct()
	{
	}


	#[AsGuardListener(self::WORKFLOW_NAME)]
	public function onGuard(GuardEvent $event): void
	{
		/** @var Pokemon pokemon */
		$pokemon = $event->getSubject();

		switch ($event->getTransition()->getName()) {
		/*
		e.g.
		if ($event->getSubject()->cannotTransition()) {
		  $event->setBlocked(true, "reason");
		}
		App\Entity\Pokemon
		*/
		    case self::TRANSITION_SCRAPE:
		        break;
		    case self::TRANSITION_RESIZE:
		        break;
		}
	}


	#[AsTransitionListener(self::WORKFLOW_NAME)]
	public function onTransition(TransitionEvent $event): void
	{
		/** @var Pokemon pokemon */
		$pokemon = $event->getSubject();

		switch ($event->getTransition()->getName()) {
		/*
		e.g.
		if ($event->getSubject()->cannotTransition()) {
		  $event->setBlocked(true, "reason");
		}
		App\Entity\Pokemon
		*/
		    case self::TRANSITION_SCRAPE:
		        break;
		    case self::TRANSITION_RESIZE:
		        break;
		}
	}
}
