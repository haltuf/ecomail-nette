<?php declare(strict_types=1);

namespace Ecomail;

class Ecomail
{
	private string $key;

	const URL = 'http://api2.ecomailapp.cz/';

    private Client $client;

	public function __construct(string $key, ?Client $client = null)
    {
		$this->key = $key;
		$this->client = $client ?? new Client($this->key);
	}
	
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

	public function deleteCampaign(string $id): array
	{
		$url = self::URL . 'campaigns/' . $id;
		return $this->client->sendRequest($url, 'DELETE');
	}

	public function sendCampaign(string $id): array
	{
		$url = self::URL . 'campaigns/' . $id . '/send';
		return $this->client->sendRequest($url, 'POST');
	}

	// Templates
	public function getTemplates(): array
	{
		$url = self::URL . 'templates';
		return $this->client->sendRequest($url);
	}

	public function getTemplate(string $id): array
	{
		$url = self::URL . 'templates/' . $id;
		return $this->client->sendRequest($url);
	}

	// Workflows
	public function getWorkflows(): array
	{
		$url = self::URL . 'workflows';
		return $this->client->sendRequest($url);
	}

	public function getWorkflow(string $id): array
	{
		$url = self::URL . 'workflows/' . $id;
		return $this->client->sendRequest($url);
	}

	// Forms
	public function getForms(): array
	{
		$url = self::URL . 'forms';
		return $this->client->sendRequest($url);
	}

	public function getForm(string $id): array
	{
		$url = self::URL . 'forms/' . $id;
		return $this->client->sendRequest($url);
	}

	// Account
	public function getAccount(): array
	{
		$url = self::URL . 'account';
		return $this->client->sendRequest($url);
	}

	// Tags
	public function getTags(): array
	{
		$url = self::URL . 'tags';
		return $this->client->sendRequest($url);
	}

	public function getTag(string $id): array
	{
		$url = self::URL . 'tags/' . $id;
		return $this->client->sendRequest($url);
	}
}
