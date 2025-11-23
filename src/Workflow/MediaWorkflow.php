<?php
declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Media;
use Survos\SaisBundle\Model\ProcessPayload;
use Survos\SaisBundle\Service\SaisClientService;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;
use App\Workflow\MediaFlowDefinition as WF;

final class MediaWorkflow
{
    public function __construct(private SaisClientService $saisClientService) {}

    private function getMedia(Event $event): Media
    {
        /** @var Media $media */
        $media = $event->getSubject();
        return $media;
    }

    #[AsTransitionListener(WF::WORKFLOW_NAME, WF::TRANSITION_DISPATCH)]
    public function onDispatch(TransitionEvent $event): void
    {
        $media = $this->getMedia($event);

        // example call; replace with your actual params
        $response = $this->saisClientService->dispatchProcess(new ProcessPayload(
            root: WF::SAIS_CODE,
            images: [$media->originalUrl]
        ));
        dd($response);

        // ...
    }

    #[AsTransitionListener(MediaFlowDefinition::WORKFLOW_NAME, MediaFlowDefinition::TRANSITION_RESIZE)]
    public function onResize(TransitionEvent $event): void
    {
        $media = $this->getMedia($event);
        // ...
    }
}
