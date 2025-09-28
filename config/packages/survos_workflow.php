<?php

declare(strict_types=1);

use Survos\StateBundle\Service\ConfigureFromAttributesService;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
//return static function (ContainerConfigurator $containerConfigurator): void {

    if (0)
    if (class_exists(ConfigureFromAttributesService::class))
        foreach ([
//                 \App\Workflow\PokemonWorkflow::class,
                 ] as $workflowClass) {
            ConfigureFromAttributesService::configureFramework($workflowClass, $framework, [$workflowClass]);
        }

};
