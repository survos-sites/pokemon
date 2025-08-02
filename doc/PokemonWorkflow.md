
Markdown for PokemonWorkflow

![PokemonWorkflow](assets/PokemonWorkflow.svg)



---
## Transition: fetch

### fetch.Transition

onFetch()
        // fetch individual JSON
        // fetch from https://pokeapi.co/api/v2/pokemon//id

```php
#[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH)]
public function onFetch(TransitionEvent $event): void
{
    $pokemon = $this->getPokemon($event);
    $url = $pokemon->getDetailUrl();

    try {
        $response = $this->httpClient->request('GET', $url);
        $statusCode = $response->getStatusCode();
        $pokemon->fetchStatusCode = $statusCode;

        if ($statusCode === 200) {
            $details = $response->toArray(false); // false = suppress exception on non-2xx
            $pokemon->setDetails($details);
        }
    } catch (\Throwable $e) {
        $this->logger->warning(sprintf('Failed to fetch details from %s: %s', $url, $e->getMessage()));
        $pokemon->setFetchStatusCode(0); // or some custom error code
    }
}
```
[View source](pokemon/blob/main/src/Workflow/PokemonWorkflow.php#L41-L59)




---
## Transition: download

### download.Transition

onDownload()
        // valid http response
        // 

```php
    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_DOWNLOAD)]
    public function onDownload(TransitionEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
//        $image = $this->rootDir . $pokemon->getImageUrl();
        $imageUrl = sprintf('https://raw.githubusercontent.com/HybridShivam/Pokemon/master/assets/images/%03d.png', $pokemon->id);
        $response = $this->saisClientService->dispatchProcess(new ProcessPayload(
             $this->rootDir,
            [$imageUrl],
            // @todo: resize callback,
        ));
        dump($response);
    }
```
[View source](pokemon/blob/main/src/Workflow/PokemonWorkflow.php#L73-L84)


