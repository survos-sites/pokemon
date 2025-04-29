Markdown for PokemonWorkflow



## completed.fetch

```php
    public function asFetchCompleted(CompletedEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
        if ($pokemon->getFetchStatusCode() !== 200) {
            $this->workflow->apply($pokemon, self::TRANSITION_FAIL_FETCH);
        }
    }
```
blob/main/src/Workflow/PokemonWorkflow.php#L58-64
        
    

## guard.download

```php
    {
        if (!isset($this->configuration[$eventName])) {
            return;
        }

        $eventConfiguration = (array) $this->configuration[$eventName];
        foreach ($eventConfiguration as $guard) {
            if ($guard instanceof GuardExpression) {
```
blob/main/vendor/symfony/workflow/EventListener/GuardListener.php#L38-55
        
    
## guard.fail_fetch

```php
    {
        if (!isset($this->configuration[$eventName])) {
            return;
        }

        $eventConfiguration = (array) $this->configuration[$eventName];
        foreach ($eventConfiguration as $guard) {
            if ($guard instanceof GuardExpression) {
```
blob/main/vendor/symfony/workflow/EventListener/GuardListener.php#L38-55
        
    

## transition.download

```php
    public function onDownload(TransitionEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
//        $image = $this->rootDir . $pokemon->getImageUrl();
        $imageUrl = sprintf('https://raw.githubusercontent.com/HybridShivam/Pokemon/master/assets/images/%03d.png', $pokemon->getId());
        $response = $this->saisClientService->dispatchProcess(new ProcessPayload(
             $this->rootDir,
            [$imageUrl],
```
blob/main/src/Workflow/PokemonWorkflow.php#L68-79
        
    
## transition.fetch

```php
    public function onFetch(TransitionEvent $event): void
    {
        $pokemon = $this->getPokemon($event);
        $url = $pokemon->getDetailUrl();
        $request = $this->httpClient->request('GET', $url);
        $statusCode = $request->getStatusCode();
        $pokemon
            ->setFetchStatusCode($statusCode);
```
blob/main/src/Workflow/PokemonWorkflow.php#L43-55
        
    