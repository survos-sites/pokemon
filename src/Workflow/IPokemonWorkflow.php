<?php

namespace App\Workflow;

use Survos\WorkflowBundle\Attribute\Place;
use Survos\WorkflowBundle\Attribute\Transition;

interface IPokemonWorkflow
{
	public const WORKFLOW_NAME = 'PokemonWorkflow';

	#[Place(initial: true)]
	public const PLACE_NEW = 'new';

	#[Place(info: "json loaded")]
	public const PLACE_FETCHED = 'fetched';

    #[Place(info: "resize requested")]
	public const PLACE_DOWNLOADED = 'downloaded';

    #[Place(info: "resize finished")]
	public const PLACE_FINISHED = 'finished';

	#[Place]
	public const PLACE_FETCH_ERROR = 'fetch_error';

	#[Transition(from: [self::PLACE_NEW], to: self::PLACE_FETCHED,
        metadata: [
            'completed' => "fail if fetchStatusCode != 200"
        ]
    )]
	public const TRANSITION_FETCH = 'fetch';

    // note: do not use <> in the comments until we properly escape them!
	#[Transition(from: [self::PLACE_FETCHED], to: self::PLACE_DOWNLOADED,
        guard: "subject.fetchStatusCode == 200",
        metadata: [
            'completed' => "fail_download if statusCode != 200"
        ]
    )]
	public const TRANSITION_DOWNLOAD = 'download';

	#[Transition(from: [self::PLACE_FETCHED], to: self::PLACE_FETCH_ERROR,
        guard: "subject.fetchStatusCode != 200",
    )]
	public const TRANSITION_FAIL_FETCH = 'fail_fetch';

	#[Transition(to: [self::PLACE_FINISHED], from: self::PLACE_DOWNLOADED)]
	public const TRANSITION_FINISH = 'resize finished';
}
