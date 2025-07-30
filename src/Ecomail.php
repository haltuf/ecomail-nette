<?php declare(strict_types=1);

namespace Ecomail;

class Ecomail
{
	private string $key;

	const URL = 'https://api2.ecomailapp.cz/';

	private Client $client;

	public function __construct(string $key, ?Client $client = null)
	{
		$this->key = $key;
		$this->client = $client ?? new Client($this->key);
	}

	// Lists
	public function getLists(): array
	{
		$url = self::URL . 'lists';

		return $this->client->sendRequest($url);
	}

	public function getList(string $id): array
	{
		$url = self::URL . 'lists/' . $id;

		return $this->client->sendRequest($url);
	}

	public function getSubscribers(string $list_id, int $page = 1): array
	{
		$url = self::URL . 'lists/' . $list_id . '/subscribers' . ($page > 1 ? '?page=' . $page : '');

		return $this->client->sendRequest($url);
	}

	public function getSubscriber(string $list_id, string $email): array
	{
		$url = self::URL . 'lists/' . $list_id . '/subscriber/' . $email;

		return $this->client->sendRequest($url);
	}

	public function addSubscriber(string $list_id, array $data = [], bool $trigger_autoresponders = false, bool $update_existing = true, bool $resubscribe = false): array
	{
		$url = self::URL . 'lists/' . $list_id . '/subscribe';
		$post = json_encode([
			'subscriber_data' => $data,
			'trigger_autoresponders' => $trigger_autoresponders,
			'update_existing' => $update_existing,
			'resubscribe' => $resubscribe,
		]);

		return $this->client->sendRequest($url, 'POST', $post);
	}

	public function deleteSubscriber(string $list_id, string $email): array
	{
		$url = self::URL . 'lists/' . $list_id . '/unsubscribe';
		$post = json_encode(['email' => $email]);

		return $this->client->sendRequest($url, 'DELETE', $post);
	}

	public function updateSubscriber(string $list_id, array $data = []): array
	{
		$url = self::URL . 'lists/' . $list_id . '/update-subscriber';
		$email = $data['email'];
		unset($data['email']);

		$post = json_encode([
			'email' => $email,
			'subscriber_data' => $data,
		]);

		return $this->client->sendRequest($url, 'PUT', $post);
	}

	// Campaigns
	public function getCampaigns(): array
	{
		$url = self::URL . 'campaigns';
		return $this->client->sendRequest($url);
	}

	public function getCampaign(string $id): array
	{
		$url = self::URL . 'campaigns/' . $id;
		return $this->client->sendRequest($url);
	}

	public function createCampaign(array $data): array
	{
		$url = self::URL . 'campaigns';
		$post = json_encode($data);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	public function updateCampaign(string $id, array $data): array
	{
		$url = self::URL . 'campaigns/' . $id;
		$post = json_encode($data);
		return $this->client->sendRequest($url, 'PUT', $post);
	}

	public function sendCampaign(string $id): array
	{
		$url = self::URL . 'campaign/' . $id . '/send';
		return $this->client->sendRequest($url, 'GET');
	}

	// Templates
	public function getTemplate(string $id): array
	{
		$url = self::URL . 'template/' . $id;
		return $this->client->sendRequest($url);
	}

	public function createTemplate(array $data): array
	{
		$url = self::URL . 'template';
		$post = json_encode($data);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	// Pipelines (Automations)
	public function getPipelines(): array
	{
		$url = self::URL . 'pipelines';
		return $this->client->sendRequest($url);
	}

	public function getPipeline(string $id): array
	{
		$url = self::URL . 'pipelines/' . $id;
		return $this->client->sendRequest($url);
	}

	public function triggerPipeline(string $pipeline_id, string $email): array
	{
		$url = self::URL . 'pipelines/' . $pipeline_id . '/trigger';
		$post = json_encode(['email' => $email]);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	// Domains
	public function getDomains(): array
	{
		$url = self::URL . 'domains';
		return $this->client->sendRequest($url);
	}

	public function createDomain(array $data): array
	{
		$url = self::URL . 'domains';
		$post = json_encode($data);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	public function deleteDomain(string $id): array
	{
		$url = self::URL . 'domains/' . $id;
		return $this->client->sendRequest($url, 'DELETE');
	}

	// Transactional emails
	public function sendTransactionalMessage(array $message): array
	{
		$url = self::URL . 'transactional/send-message';
		$post = json_encode(['message' => $message]);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	public function sendTransactionalTemplate(array $message): array
	{
		$url = self::URL . 'transactional/send-template';
		$post = json_encode(['message' => $message]);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	// Transactions
	public function createTransaction(array $transaction, array $transaction_items): array
	{
		$url = self::URL . 'tracker/transaction';
		$post = json_encode([
			'transaction' => $transaction,
			'transaction_items' => $transaction_items
		]);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	public function updateTransaction(string $order_id, array $transaction, array $transaction_items): array
	{
		$url = self::URL . 'tracker/transaction/' . $order_id;
		$post = json_encode([
			'transaction' => $transaction,
			'transaction_items' => $transaction_items
		]);
		return $this->client->sendRequest($url, 'PUT', $post);
	}

	public function deleteTransaction(string $order_id, ?string $shop = null): array
	{
		$url = self::URL . 'tracker/transaction/' . $order_id . '/delete';
		$post = $shop ? json_encode(['transaction_data' => ['shop' => $shop]]) : null;
		return $this->client->sendRequest($url, 'DELETE', $post);
	}

	// Global subscriber operations
	public function getGlobalSubscriber(string $email): array
	{
		$url = self::URL . 'subscribers/' . $email;
		return $this->client->sendRequest($url);
	}

	public function deleteGlobalSubscriber(string $email): array
	{
		$url = self::URL . 'subscribers/' . $email . '/delete';
		return $this->client->sendRequest($url, 'DELETE');
	}

	// Search
	public function search(string $email): array
	{
		$url = self::URL . 'search';
		$post = json_encode(['query' => $email]);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	// Webhook
	public function setWebhook(string $webhook_url): array
	{
		$url = self::URL . 'account/settings/webhook';
		$post = json_encode(['url' => $webhook_url]);
		return $this->client->sendRequest($url, 'POST', $post);
	}

	public function getWebhook(): array
	{
		$url = self::URL . 'account/settings/webhook';
		return $this->client->sendRequest($url);
	}

	public function deleteWebhook(): array
	{
		$url = self::URL . 'account/settings/webhook';
		return $this->client->sendRequest($url, 'DELETE');
	}
}