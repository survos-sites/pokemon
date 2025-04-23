Markdown for PokemonWorkflow



## guard.

```php
	public function onGuard(GuardEvent $event): void
	{
		/** @var Pokemon pokemon */
		$pokemon = $event->getSubject();

		switch ($event->getTransition()->getName()) {
		/*
		e.g.
```
blob/main/src/Workflow/PokemonWorkflow.php#L23-41
        
    

## transition.

```php
	public function onTransition(TransitionEvent $event): void
	{
		/** @var Pokemon pokemon */
		$pokemon = $event->getSubject();

		switch ($event->getTransition()->getName()) {
		/*
		e.g.
```
blob/main/src/Workflow/PokemonWorkflow.php#L45-63
        
    