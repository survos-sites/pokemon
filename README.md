# OnsenUI Tutorial

A mobile pokemon-collection app, based on https://onsen.io/v2/guide/tutorial.html

It has been converted to Symfony.

## Setup

```bash
git clone git@github.com:survos-sites/pokemon.git && cd pokemon
composer install
bin/console doctrine:fixtures:load -n
symfony server:start -d
symfony open:local
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




