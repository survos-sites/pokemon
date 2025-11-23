<?php

namespace App\Controller\Admin;

use Survos\EzBundle\Controller\BaseCrudController;

class PokemonCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return \App\Entity\Pokemon::class;
    }
}
