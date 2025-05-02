Markdown for PokemonWorkflow

![PokemonWorkflow.svg](PokemonWorkflow.svg)



## download -- transition


```php
    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_DOWNLOAD)]
    public function onDownload(TransitionEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
//        $image = $this->rootDir . $pokemon->getImageUrl();
        $imageUrl = sprintf('https://raw.githubusercontent.com/HybridShivam/Pokemon/master/assets/images/%03d.png', $pokemon->getId());
        $response = $this->saisClientService->dispatchProcess(new ProcessPayload(
             $this->rootDir,
            [$imageUrl],
            // @todo: resize callback,
        ));
        dump($response);
    }
```
blob/main/src/Workflow/PokemonWorkflow.php#L68-79
        

## fetch -- transition


```php
#[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH)]
public function onFetch(TransitionEvent $event): void
{
    $pokemon = $this->getPokemon($event);
    $url = $pokemon->getDetailUrl();
    $request = $this->httpClient->request('GET', $url);
    $statusCode = $request->getStatusCode();
    $pokemon
        ->setFetchStatusCode($statusCode);
    if ($statusCode === 200) {
        $details = json_decode($request->getContent(), true);
        $pokemon->setDetails($details);
    }
}
```
blob/main/src/Workflow/PokemonWorkflow.php#L43-55
        


## fetch -- completed


```php
#[AsCompletedListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH)]
public function asFetchCompleted(CompletedEvent $event): void
{
    $pokemon = $this->getPokemon($event);
    if ($pokemon->getFetchStatusCode() !== 200) {
        $this->workflow->apply($pokemon, self::TRANSITION_FAIL_FETCH);
    }
}
```
blob/main/src/Workflow/PokemonWorkflow.php#L58-64
        
