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



