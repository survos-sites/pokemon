<?php

namespace App\Workflow;

use Survos\WorkflowBundle\Attribute\Place;
use Survos\WorkflowBundle\Attribute\Transition;

interface IPokemonWorkflow
{
	public const WORKFLOW_NAME = 'PokemonWorkflow';

	#[Place(initial: true)]
	public const PLACE_NEW = 'new';

	#[Place]
	public const PLACE_SCRAPED = 'scraped';

	#[Place]
	public const PLACE_RESIZED = 'resized';

	#[Transition(from: [self::PLACE_NEW], to: self::PLACE_SCRAPED)]
	public const TRANSITION_SCRAPE = 'scrape';

	#[Transition(from: [self::PLACE_SCRAPED], to: self::PLACE_RESIZED)]
	public const TRANSITION_RESIZE = 'resize';
}
