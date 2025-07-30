<?php declare(strict_types=1);

namespace Price2Performace\SendGrid\Tests;

require __DIR__ . '/../Bootstrap.php';

use Ecomail\Ecomail;
use Ecomail\Tests\Bootstrap;
use Nette\DI\Container;
use Nette\Application\Request;
use Tester\Assert;
use Tester\TestCase;

class SendGridExtensionTest extends TestCase
{
	private Container $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function testDI()
	{
		$container = $this->container;

		Assert::type(Ecomail::class, $container->getByName('ecomail.service'));
	}

	public function testInject()
	{
		$container = $this->container;

		$presenterFactory = $container->getByType(\Nette\Application\IPresenterFactory::class);
		$presenter = $presenterFactory->createPresenter('Test');
		$presenter->autoCanonicalize = false;
		$request = new Request('Test', 'GET', ['action' => 'default']);
		$response = $presenter->run($request);

		Assert::same(Ecomail::class, (string) $response->getSource());
	}

}

$container = Bootstrap::bootForTests()->createContainer();
(new SendGridExtensionTest($container))->run();