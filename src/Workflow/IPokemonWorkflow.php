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
	public const PLACE_FETCHED = 'fetched';

	#[Place]
	public const PLACE_DOWNLOADED = 'downloaded';

	#[Place]
	public const PLACE_FINISHED = 'finished';

	#[Place]
	public const PLACE_FETCH_ERROR = 'fetch_error';

	#[Place]
	public const PLACE_DOWNLOAD_ERROR = 'download_error';

	#[Transition(from: [self::PLACE_NEW], to: self::PLACE_FETCHED,
        metadata: [
            'completed' => "fail if statusCode != 200"
        ]
    )]
	public const TRANSITION_FETCH = 'fetch';

    // note: do not use <> in the comments until we properly escape them!
	#[Transition(from: [self::PLACE_FETCHED], to: self::PLACE_DOWNLOADED,
        guard: "subject.statusCode != 200",
        metadata: [
            'completed' => "fail_download if statusCode != 200"
        ]
    )]
	public const TRANSITION_DOWNLOAD = 'download';

	#[Transition(from: [self::PLACE_FETCHED], to: self::PLACE_FETCH_ERROR,
        guard: "subject.statusCode != 200",
    )]
	public const TRANSITION_FAIL_FETCH = 'fail_fetch';

	#[Transition(from: [self::PLACE_FINISHED], to: self::PLACE_FETCH_ERROR)]
	public const TRANSITION_FAIL_DOWNLOAD = 'fail_download';
}
