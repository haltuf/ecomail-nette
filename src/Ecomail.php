<?php declare(strict_types=1);

namespace Ecomail;

class Ecomail
{
	private string $key;
	
	const URL = 'http://api2.ecomailapp.cz/';

	public function __construct(string $key)
    {
		$this->key = $key;
	}
	
	private function sendRequest(string $url, string $request = 'POST', string $data = ''): array
	{
		$http_headers = array();
		$http_headers[] = "key: " . $this->key;
		$http_headers[] = "Content-Type: application/json";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
		
		if (!empty($data)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			
			if ($request === 'POST') {
				curl_setopt($ch, CURLOPT_POST, TRUE);
			} else {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
			}
		}
		
		$result = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($result);
	}
	
	public function getLists(): array
	{
		$url = self::URL . 'lists';
		
		return $this->sendRequest($url);
	}

	public function getList(string $id): array
	{
		$url = self::URL . 'lists/' . $id;
		
		return $this->sendRequest($url);
	}
	
	public function getSubscribers(string $list_id, int $page = 1): array
	{
		$url = self::URL . 'lists/' . $list_id . '/subscribers' . ($page > 1 ? '?page=' . $page : '');
		
		return $this->sendRequest($url);
	}
	
	public function getSubscriber(string $list_id, string $email): array
	{
		$url = self::URL . 'lists/' . $list_id . '/subscriber/' . $email;
		
		return $this->sendRequest($url);
	}
	
	public function addSubscriber(string $list_id, array $data = [], bool $trigger_autoresponders = false, bool $update_existing = true, bool $resubscribe = false): array
	{
		$url = self::URL . 'lists/' . $list_id . '/subscribe';
		$post = json_encode([
			'subscriber_data' => [
				'name' => $data['name'],
				'surname' => $data['surname'],
				'email' => $data['email'],
				'vokativ' => $data['vokativ'],
				'vokativ_s' => $data['vokativ_s'],
				'company' => $data['company'],
				'city' => $data['city'],
				'street' => $data['street'],
				'zip' => $data['zip'],
				'country' => $data['country'],
				'phone' => $data['phone'],
				'pretitle' => $data['pretitle'],
				'surtitle' => $data['surtitle'],
				'birthday' => $data['birthday'],
				'custom_fields' => (array)$data['custom_fields'],
			],
			'trigger_autoresponders' => $trigger_autoresponders,
			'update_existing' => $update_existing,
			'resubscribe' => $resubscribe,
		]);
		
		return $this->sendRequest($url, 'POST', $post);
	}
	
	public function deleteSubscriber(string $list_id, string $email): array
	{
		$url = self::URL . 'lists/' . $list_id . '/unsubscribe';
		$post = json_encode(['email' => $email]);
		
		return $this->sendRequest($url, 'DELETE', $post);
	}
	
	public function updateSubscriber(string $list_id, array $data = []): array
	{
		$url = self::URL . 'lists/' . $list_id . '/update-subscriber';
		$post = json_encode([
			'email' => $data['email'],
			'subscriber_data' => [
				'name' => $data['name'],
				'surname' => $data['surname'],
				'vokativ' => $data['vokativ'],
				'vokativ_s' => $data['vokativ_s'],
				'company' => $data['company'],
				'city' => $data['city'],
				'street' => $data['street'],
				'zip' => $data['zip'],
				'country' => $data['country'],
				'phone' => $data['phone'],
				'pretitle' => $data['pretitle'],
				'surtitle' => $data['surtitle'],
				'birthday' => $data['birthday'],
				'custom_fields' => (array)$data['custom_fields'],
			],
		]);
		
		return $this->sendRequest($url, 'PUT', $post);
	}
}
