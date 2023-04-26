<?php declare(strict_types=1);

namespace Ecomail;

use Nette\DI\CompilerExtension;


class Extension extends CompilerExtension
{
	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig();

		$service = $container->addDefinition($this->prefix('service'))
			->setFactory('Ecomail\Ecomail', [
				$config['key'],
			]);
	}
}
