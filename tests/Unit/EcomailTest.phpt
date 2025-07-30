<?php declare(strict_types=1);

namespace Ecomail\Tests\Unit;

require __DIR__ . '/../Bootstrap.php';

use Ecomail\Client;
use Ecomail\Ecomail;
use Ecomail\Tests\Bootstrap;
use Mockery;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

class EcomailTest extends TestCase
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

	public function testGetList(): void
	{
		$expectedResponse = [
			'id' => '123',
			'name' => 'Test List',
			'created' => '2024-01-01 10:00:00',
			'subscribers_count' => 1500,
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123')
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getList('123');

		Assert::same($expectedResponse, $result);
	}

	public function testGetSubscribers(): void
	{
		$expectedResponse = [
			'subscribers' => [
				[
					'email' => 'user1@example.com',
					'name' => 'User 1',
				],
				[
					'email' => 'user2@example.com',
					'name' => 'User 2',
				],
			],
			'page' => 1,
			'total' => 2,
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123/subscribers')
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getSubscribers('123');

		Assert::same($expectedResponse, $result);
	}

	public function testGetSubscribersWithPagination(): void
	{
		$expectedResponse = [
			'subscribers' => [
				[
					'email' => 'user3@example.com',
					'name' => 'User 3',
				],
			],
			'page' => 2,
			'total' => 3,
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123/subscribers?page=2')
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getSubscribers('123', 2);

		Assert::same($expectedResponse, $result);
	}

	public function testGetSubscriber(): void
	{
		$expectedResponse = [
			'email' => 'user@example.com',
			'name' => 'Test User',
			'subscribed' => true,
			'created' => '2024-01-01 10:00:00',
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123/subscriber/user@example.com')
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getSubscriber('123', 'user@example.com');

		Assert::same($expectedResponse, $result);
	}

	public function testAddSubscriber(): void
	{
		$subscriberData = [
			'email' => 'newuser@example.com',
			'name' => 'New User',
			'city' => 'Prague',
		];

		$expectedResponse = [
			'success' => true,
			'subscriber' => $subscriberData,
		];

		$expectedPostData = json_encode([
			'subscriber_data' => $subscriberData,
			'trigger_autoresponders' => false,
			'update_existing' => true,
			'resubscribe' => false,
		]);

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123/subscribe', 'POST', $expectedPostData)
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->addSubscriber('123', $subscriberData);

		Assert::same($expectedResponse, $result);
	}

	public function testAddSubscriberWithOptions(): void
	{
		$subscriberData = [
			'email' => 'newuser@example.com',
			'name' => 'New User',
		];

		$expectedResponse = [
			'success' => true,
			'subscriber' => $subscriberData,
		];

		$expectedPostData = json_encode([
			'subscriber_data' => $subscriberData,
			'trigger_autoresponders' => true,
			'update_existing' => false,
			'resubscribe' => true,
		]);

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123/subscribe', 'POST', $expectedPostData)
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->addSubscriber('123', $subscriberData, true, false, true);

		Assert::same($expectedResponse, $result);
	}

	public function testDeleteSubscriber(): void
	{
		$expectedResponse = [
			'success' => true,
			'message' => 'Subscriber unsubscribed',
		];

		$expectedPostData = json_encode(['email' => 'user@example.com']);

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123/unsubscribe', 'DELETE', $expectedPostData)
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->deleteSubscriber('123', 'user@example.com');

		Assert::same($expectedResponse, $result);
	}

	public function testUpdateSubscriber(): void
	{
		$updateData = [
			'email' => 'user@example.com',
			'name' => 'Updated Name',
			'city' => 'Brno',
			'custom_field' => 'value',
		];

		$expectedResponse = [
			'success' => true,
			'subscriber' => $updateData,
		];

		$expectedPostData = json_encode([
			'email' => 'user@example.com',
			'subscriber_data' => [
				'name' => 'Updated Name',
				'city' => 'Brno',
				'custom_field' => 'value',
			],
		]);

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/lists/123/update-subscriber', 'PUT', $expectedPostData)
			->andReturn($expectedResponse);

		$ecomail = new Ecomail('API_KEY', $clientMock);

		$result = $ecomail->updateSubscriber('123', $updateData);

		Assert::same($expectedResponse, $result);
	}

	public function testGetCampaigns(): void
	{
		$expectedResponse = [
			['id' => '1', 'name' => 'Campaign 1'],
			['id' => '2', 'name' => 'Campaign 2'],
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/campaigns')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getCampaigns();

		Assert::same($expectedResponse, $result);
	}

	public function testGetCampaign(): void
	{
		$expectedResponse = ['id' => '1', 'name' => 'Campaign 1'];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/campaigns/1')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getCampaign('1');

		Assert::same($expectedResponse, $result);
	}

	public function testCreateCampaign(): void
	{
		$data = ['name' => 'New Campaign'];
		$expectedResponse = ['id' => '3', 'name' => 'New Campaign'];
		$expectedPostData = json_encode($data);

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/campaigns', 'POST', $expectedPostData)
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->createCampaign($data);

		Assert::same($expectedResponse, $result);
	}

	public function testUpdateCampaign(): void
	{
		$data = ['name' => 'Updated Campaign'];
		$expectedResponse = ['success' => true];
		$expectedPostData = json_encode($data);

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/campaigns/3', 'PUT', $expectedPostData)
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->updateCampaign('3', $data);

		Assert::same($expectedResponse, $result);
	}

	public function testDeleteCampaign(): void
	{
		$expectedResponse = ['success' => true];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/campaigns/3', 'DELETE')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->deleteCampaign('3');

		Assert::same($expectedResponse, $result);
	}

	public function testSendCampaign(): void
	{
		$expectedResponse = ['success' => true];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/campaigns/3/send', 'POST')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->sendCampaign('3');

		Assert::same($expectedResponse, $result);
	}

	public function testGetTemplates(): void
	{
		$expectedResponse = [
			['id' => '1', 'name' => 'Template 1'],
			['id' => '2', 'name' => 'Template 2'],
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/templates')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getTemplates();

		Assert::same($expectedResponse, $result);
	}

	public function testGetTemplate(): void
	{
		$expectedResponse = ['id' => '1', 'name' => 'Template 1'];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/templates/1')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getTemplate('1');

		Assert::same($expectedResponse, $result);
	}

	public function testGetWorkflows(): void
	{
		$expectedResponse = [
			['id' => '1', 'name' => 'Workflow 1'],
			['id' => '2', 'name' => 'Workflow 2'],
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/workflows')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getWorkflows();

		Assert::same($expectedResponse, $result);
	}

	public function testGetWorkflow(): void
	{
		$expectedResponse = ['id' => '1', 'name' => 'Workflow 1'];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/workflows/1')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getWorkflow('1');

		Assert::same($expectedResponse, $result);
	}

	public function testGetForms(): void
	{
		$expectedResponse = [
			['id' => '1', 'name' => 'Form 1'],
			['id' => '2', 'name' => 'Form 2'],
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/forms')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getForms();

		Assert::same($expectedResponse, $result);
	}

	public function testGetForm(): void
	{
		$expectedResponse = ['id' => '1', 'name' => 'Form 1'];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/forms/1')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getForm('1');

		Assert::same($expectedResponse, $result);
	}

	public function testGetAccount(): void
	{
		$expectedResponse = [
			'id' => '1',
			'email' => 'admin@example.com',
			'credits' => 10000,
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/account')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getAccount();

		Assert::same($expectedResponse, $result);
	}

	public function testGetTags(): void
	{
		$expectedResponse = [
			['id' => '1', 'name' => 'VIP'],
			['id' => '2', 'name' => 'Newsletter'],
		];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/tags')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getTags();

		Assert::same($expectedResponse, $result);
	}

	public function testGetTag(): void
	{
		$expectedResponse = ['id' => '1', 'name' => 'VIP'];

		$clientMock = Mockery::mock(Client::class);
		$clientMock->shouldReceive('sendRequest')
			->once()
			->with('http://api2.ecomailapp.cz/tags/1')
			->andReturn($expectedResponse);

		$ecomail = new \Ecomail\Ecomail('API_KEY', $clientMock);

		$result = $ecomail->getTag('1');

		Assert::same($expectedResponse, $result);
	}

	public function tearDown(): void
    {
        Mockery::close();
    }
}

$container = Bootstrap::bootForTests()->createContainer();
(new EcomailTest($container))->run();