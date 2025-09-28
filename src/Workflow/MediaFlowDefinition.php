<?php
declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Media;
use Survos\StateBundle\Attribute\Workflow;
use Survos\StateBundle\Attribute\Place;
use Survos\StateBundle\Attribute\Transition;

#[Workflow(
    name: self::WORKFLOW_NAME,
    type: 'state_machine',
    supports: [Media::class],
)]
final class MediaFlowDefinition
{
    public const WORKFLOW_NAME = 'MediaFlow';
    public const SAIS_CODE='pokemon';

    // Places FIRST
    #[Place(initial: true)]
    public const PLACE_NEW = 'new';

    #[Place]
    public const PLACE_DISPATCHED = 'dispatched';

    #[Place]
    public const PLACE_RESIZED = 'resized';

    // Transitions AFTER places
    #[Transition(from: [self::PLACE_NEW], to: self::PLACE_DISPATCHED, async: true)]
    public const TRANSITION_DISPATCH = 'dispatch';

    #[Transition(from: [self::PLACE_DISPATCHED], to: self::PLACE_RESIZED)]
    public const TRANSITION_RESIZE = 'resize';
}
