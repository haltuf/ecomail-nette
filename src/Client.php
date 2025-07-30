<?php declare(strict_types=1);

namespace Ecomail;

class Client
{
	private string $key;

    public function __construct(string $key) {
		$this->key = $key;
	}

    public function sendRequest(string $url, string $request = 'POST', string $data = ''): array
    {
        $http_headers = array();
        $http_headers[] = "key: " . $this->key;
        $http_headers[] = "Content-Type: application/json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            if ($request === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
            }
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}