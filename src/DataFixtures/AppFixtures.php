<?php

namespace App\DataFixtures;

use App\Entity\Pokemon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Survos\Scraper\Service\ScraperService;

class AppFixtures extends Fixture
{
    public function __construct(private readonly ScraperService $scraper)
    {

    }
    public function load(ObjectManager $manager): void
    {
        // this should only be used for phpunit tests! For production, use bin/console app:load

        // $product = new Product();
        // $manager->persist($product);
        $response = $this->scraper->fetchData('https://pokeapi.co/api/v2/pokemon',
            ['limit' => 2], asData: 'object');
        foreach ($response->results as $idx => $data) {
            $details = $this->scraper->fetchData($data->url);
            $poke = (new Pokemon($details['id'], $data->name))
                ->setDetails($details)
            ;
            $poke->setOwned(in_array($idx, [2,3,5,8,13,21]));
            $manager->persist($poke);
        }
        $manager->flush();

    }
}
