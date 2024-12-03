<?php

namespace App\Command;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Survos\Scraper\Service\ScraperService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Zenstruck\Console\Attribute\Option;
use Zenstruck\Console\InvokableServiceCommand;
use Zenstruck\Console\IO;
use Zenstruck\Console\RunsCommands;
use Zenstruck\Console\RunsProcesses;
use function Symfony\Component\String\u;

#[AsCommand('app:load', 'Loads pokemon data from the api')]
final class AppLoadCommand extends InvokableServiceCommand
{
    use RunsCommands;
    use RunsProcesses;

    public function __invoke(
        IO $io,
        EntityManagerInterface $entityManager,
        PokemonRepository $pokemonRepository,
        ScraperService $scraper,

        #[Option(description: 'limit the number of items loaded')]
        int $limit,
        #[Option(description: 'drop all items before loading')]
        bool $reset = true,
    ): int {
        $existing = [];
            foreach ($pokemonRepository->findAll() as $pokemon) {
                if ($reset) {
                    $entityManager->remove($pokemon);
                } else {
                    $existing[$pokemon->getId()] = $pokemon;
                }
            }
            if ($reset) {
                $entityManager->flush();
                $io->warning('All pokemons have been successfully removed.');
            }

        // get the count
        $response = $scraper->fetchData('https://pokeapi.co/api/v2/pokemon',
            ['limit' => 2000], asData: 'object');
            $total = $response->count;
        $progressBar = new ProgressBar($io->output(), $total);

        foreach ($response->results as $idx => $data) {
            $progressBar->advance();
            if (preg_match('|/(\d+)/|', $data->url, $matches)) {
                $id = (int)$matches[1];
            } else {
                throw new \Exception("bad url " . $data->url);
            }
            assert($id, $id . " " . $data->url);
            if (!$poke = $existing[$id] ?? null) {
                $poke = (new Pokemon($id));
                $poke->setOwned(in_array($idx, [2,3,5,8,13,21]));
                $entityManager->persist($poke);
            }
            $details = $scraper->fetchData($data->url);
            $poke
                ->setDetails($details)
                ->setName($data->name);
            if ($limit && ($idx >= $limit)) {
                break;
            }
            $entityManager->flush();
        }
        $progressBar->finish();

        $io->success($this->getName().' success. ' . $pokemonRepository->count());

        return self::SUCCESS;
    }
}
