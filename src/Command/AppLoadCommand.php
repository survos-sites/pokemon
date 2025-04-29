<?php

namespace App\Command;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Survos\Scraper\Service\ScraperService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Symfony\Component\String\u;

#[AsCommand('app:load', 'Loads pokemon data from the api')]
final class AppLoadCommand
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PokemonRepository      $pokemonRepository,
        private ScraperService         $scraper,

    )
    {
    }

    public function __invoke(
        SymfonyStyle                                                   $io,

        #[Option(description: 'limit the number of items loaded')] int $limit = -1,
        #[Option(description: 'first item')] int                       $start = 0,
        #[Option(description: 'drop all items before loading')]
        ?bool                                                          $reset = null,
    ): int
    {
        $reset ??= false;
        $existing = [];
        foreach ($this->pokemonRepository->findAll() as $pokemon) {
            if ($reset) {
                $this->entityManager->remove($pokemon);
            } else {
                $existing[$pokemon->getId()] = $pokemon;
            }
        }
        if ($reset) {
            $this->entityManager->flush();
            $io->warning('All pokemon have been successfully removed.');
        }

        // get the count
        $response = $this->scraper->fetchData(Pokemon::BASE_URL,
            ['limit' => 2000], asData: 'object');
        $total = $response->count;

        $limit = $limit ? min($limit, $total) : $total;
        $progressBar = new ProgressBar($io, $total);
        $slice = new \LimitIterator(new \ArrayIterator($response->results), $start, $limit);
        foreach ($progressBar->iterate($slice) as $idx => $data) {

            if (preg_match('|/(\d+)/|', $data->url, $matches)) {
                $id = $matches[1];
            } else {
                throw new \Exception("bad url " . $data->url);
            }
            assert($id, $id . " " . $data->url);
            if (!$poke = $existing[$id] ?? null) {
                $poke = (new Pokemon($id));
                $poke->setOwned(in_array($idx, [2, 3, 5, 8, 13, 21]));
                $this->entityManager->persist($poke);
            }
            $poke->setName($data->name);
            // moved to workflow
//            $poke->setDetails($scraper->fetchData($data->url));
        }
        $this->entityManager->flush();
        $io->success(self::class . ' success. ' . $this->pokemonRepository->count());

        return Command::SUCCESS;
    }
}
