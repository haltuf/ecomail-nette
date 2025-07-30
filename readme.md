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

případně pokud stále používáte PHP 7.4, tak:

```php
	/** @inject */
	public Ecomail\Ecomail ecomail;
```

A následně můžete používat:

```php
	// Seznamy kontaktů
	// ----------------
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
	$this->ecomail->addSubscriber(1, array('email' => 'example@example.com'), false, true, true);

	// smazání odběratele
	$this->ecomail->deleteSubscriber(1, 'example@example.com');
	
	// aktualizace odběratele
	$this->ecomail->updateSubscriber(1, array('email' => 'example@example.com', 'name' => 'Nové jméno'));

	// Kampaně
	// -------
	// získá všechny kampaně
	$this->ecomail->getCampaigns();
	
	// získá konkrétní kampaň
	$this->ecomail->getCampaign('campaign_id');
	
	// vytvoří novou kampaň
	$this->ecomail->createCampaign(array('name' => 'Nová kampaň', 'subject' => 'Předmět', 'list_id' => 1));
	
	// aktualizuje existující kampaň
	$this->ecomail->updateCampaign('campaign_id', array('name' => 'Upravená kampaň'));
	
	// odešle kampaň
	$this->ecomail->sendCampaign('campaign_id');
	
	// Šablony
	// -------
	// získá konkrétní šablonu
	$this->ecomail->getTemplate('template_id');
	
	// vytvoří novou šablonu
	$this->ecomail->createTemplate(array('name' => 'Nová šablona', 'content' => '<html>...</html>'));
	
	// Automatizace (Pipelines)
	// -----------------------
	// získá všechny automatizace
	$this->ecomail->getPipelines();
	
	// získá konkrétní automatizaci
	$this->ecomail->getPipeline('pipeline_id');
	
	// spustí automatizaci pro konkrétní e-mail
	$this->ecomail->triggerPipeline('pipeline_id', 'example@example.com');
	
	// Domény
	// ------
	// získá všechny domény
	$this->ecomail->getDomains();
	
	// vytvoří novou doménu
	$this->ecomail->createDomain(array('domain' => 'example.com'));
	
	// smaže doménu
	$this->ecomail->deleteDomain('domain_id');
	
	// Transakční e-maily
	// -----------------
	// odešle transakční e-mail
	$this->ecomail->sendTransactionalMessage(array(
		'to' => 'recipient@example.com',
		'subject' => 'Předmět',
		'from_email' => 'sender@example.com',
		'from_name' => 'Odesílatel',
		'html' => '<p>Obsah e-mailu</p>'
	));
	
	// odešle transakční e-mail pomocí šablony
	$this->ecomail->sendTransactionalTemplate(array(
		'to' => 'recipient@example.com',
		'template_id' => 'template_id',
		'from_email' => 'sender@example.com',
		'from_name' => 'Odesílatel',
		'variables' => array('var1' => 'hodnota1')
	));
	
	// Transakce
	// ---------
	// vytvoří novou transakci
	$this->ecomail->createTransaction(
		array('email' => 'customer@example.com', 'order_id' => '123', 'amount' => 1000),
		array(array('code' => 'PROD1', 'title' => 'Produkt 1', 'price' => 1000, 'quantity' => 1))
	);
	
	// aktualizuje existující transakci
	$this->ecomail->updateTransaction(
		'123',
		array('email' => 'customer@example.com', 'amount' => 1500),
		array(array('code' => 'PROD1', 'title' => 'Produkt 1', 'price' => 1500, 'quantity' => 1))
	);
	
	// smaže transakci
	$this->ecomail->deleteTransaction('123');
	
	// Globální operace s odběrateli
	// ----------------------------
	// získá informace o odběrateli napříč všemi seznamy
	$this->ecomail->getGlobalSubscriber('example@example.com');
	
	// smaže odběratele ze všech seznamů
	$this->ecomail->deleteGlobalSubscriber('example@example.com');
	
	// Vyhledávání
	// ----------
	// vyhledá odběratele podle e-mailu
	$this->ecomail->search('example@example.com');
	
	// Webhook
	// -------
	// nastaví webhook URL
	$this->ecomail->setWebhook('https://example.com/webhook');
	
	// získá aktuální webhook URL
	$this->ecomail->getWebhook();
	
	// smaže webhook URL
	$this->ecomail->deleteWebhook();
```