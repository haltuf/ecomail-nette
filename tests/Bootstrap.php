<?php declare(strict_types=1);

namespace Ecomail\Tests;

require __DIR__ . '/../vendor/autoload.php';

use Nette\Bootstrap\Configurator;

class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;


		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(self::getTempDir());

		$configurator->createRobotLoader()
			->addDirectory(__DIR__ . '/fixtures')
			->register();

		$configurator
			->addConfig(__DIR__ . '/Unit/config.neon');

		return $configurator;
	}


	public static function bootForTests(): Configurator
	{
		\Tester\Helpers::purge(self::getTempDir());
		$configurator = self::boot();
		\Tester\Environment::setup();
		return $configurator;
	}

	public static function getTempDir(): string
	{
		return __DIR__ . '/tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid());
	}
}