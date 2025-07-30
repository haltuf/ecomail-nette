Ecomail API for Nette
=====================

Jednoduchá implementace Ecomail API pro Nette, pokrývá jen část rozsahu celého API. Za případné rozšíření budu rád, v současné chvíli jsem pro ostatní funkce neměl využití.

Autor nemá žádné spojení s firmou Ecomail ani s Nette Framework.

Požadavky
------------ 
- PHP 7.4-8.4 a Nette 3 (v0.2)

Instalace
------------

Nejjednodušším způsobem instalace je přidat pomocí příkazu

```
composer require haltuf/ecomail-nette
```

Použtí
-----

Do souboru `config.neon` přidejte následující řádky:

```
extensions:
	ecomail: Ecomail\Extension

ecomail:
	key: YOUR_KEY_HERE
```

Službu si injectněte do Presenteru:

```php
	#[Inject]
	public Ecomail\Ecomail $ecomail;
```

A následně můžete používat:

```php
	// získá všechny seznamy kontaktů
	$this->ecomail->getLists();

	// získá konkrétní seznam kontaktů
	$this->ecomail->getList(1);

	// získá odběratele newsletteru, stránkování funguje po 20
	$this->ecomail->getSubscribers(1, $page);

	// získá konkrétního odběratele ze seznamu kontaktů ID 1
	$this->ecomail->getSubscriber(1, 'example@example.com');

	// vytvoří nového odběratele, pokud existuje, tak ho upraví
	// pro seznam všech možných hodnot se podívejte na kód funkce
	$this->ecomail->addSubscriber(1, array('email' => 'example@example.com', false, true, true));

	// smazání odběratele
	$this->ecomail->deleteSubscriber(1, 'example@example.com');
```