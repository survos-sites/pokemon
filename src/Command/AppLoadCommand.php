<?php

namespace App\Command;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use App\Workflow\MediaFlowDefinition;
use Doctrine\ORM\EntityManagerInterface;
use Survos\SaisBundle\Model\AccountSetup;
use Survos\SaisBundle\Service\SaisClientService;
use Survos\Scraper\Service\ScraperService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use function Symfony\Component\String\u;

#[AsCommand('load:Pokemon', 'Loads pokemon data from the api at ' . Pokemon::BASE_URL)]
final class AppLoadCommand
{

    public function __construct(
        private EntityManagerInterface                 $entityManager,
        private PokemonRepository                      $pokemonRepository,
        private ScraperService                         $scraper,
        private readonly SaisClientService $saisClientService,

    )
    {
    }

    public function __invoke(
        SymfonyStyle                                                   $io,

        #[Option(description: 'limit the number of items loaded')] int $limit = -1,
        #[Option(description: 'first item')] int                       $start = 0,
        #[Option(description: 'drop all items before loading')]
        ?bool                                                          $reset = null,
        #[Option('dispatch sais setup')]
        ?bool                                                          $sais = null,

    ): int
    {
        if ($sais) {
            $this->saisClientService->accountSetup(
                new AccountSetup(MediaFlowDefinition::SAIS_CODE, 2000)
            );
        }
        $reset ??= false;
        $existing = [];
        foreach ($this->pokemonRepository->findAll() as $pokemon) {
            if ($reset) {
                $this->entityManager->remove($pokemon);
            } else {
                $existing[$pokemon->id] = $pokemon;
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
                $poke = (new Pokemon($id, $data->name));
                $poke->setOwned(in_array($idx, [2, 3, 5, 8, 13, 21]));
                $this->entityManager->persist($poke);
            }
        }
        $this->entityManager->flush();
        $io->success(self::class . ' success. ' . $this->pokemonRepository->count());

        return Command::SUCCESS;
    }
}
