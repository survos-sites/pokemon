# OnsenUI Tutorial

A mobile pokemon-collection app, based on https://onsen.io/v2/guide/tutorial.html

It has been converted to Symfony.

## Fixtures

Fixtures bypass the workflow, so are faster to set up for tests.

## Normal Setup

```bash
git clone git@github.com:survos-sites/pokemon.git && cd pokemon
composer install
bin/console doctrine:schema:update --force
bin/console app:load --limit 50
symfony server:start -d
symfony open:local
```

## Workflows

This is the first bundle to use survos/state-bundle with dynamic async queues (doctrine).
At the moment, it still requires rabbitMq for the meili updates.
Best practice is to use postgres.

Now fetch the data

```bash
bin/console workflow:iterate App\\Entity\\Pokemon --marking=new --transition=fetch -vv
```


After opening the page, do a shift-refresh to re-load the page and the pokemon list will show up.

The database only exists on the user's device, so there's no database or security to set up.


## Survos Bundle Developers

In order to make changes to the survos bundles, you must link the survos/survos packages locally.

Put the survos repo in the same directory as the pokemon repo.

```bash
git clone git@github.com:survos/survos.git && cd pokemon
../survos/link .
ls -l vendor/survos
```

Now the pokemon vendor library points to the local repo, so changes make to javascript and css are reflected.

In your IDE, don't link all of survos, just *attach* the packages you are working on, e.g. 

    ../survos/packages/js-twig-bundle

## @todo

Update the load to use https://raw.githubusercontent.com/Biuni/PokemonGO-Pokedex/master/pokedex.json


## Tutorial

bin/console survos:workflow:generate App\\Entity\\Pokemon new,fetched,downloaded,finished,fetch_error,download_error fetch,download,fail_fetch,fail_download
