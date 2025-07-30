<?php declare(strict_types=1);

use Ecomail\Ecomail;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use Nette\DI\Container;

class TestPresenter extends Presenter
{
	#[Inject]
	public Ecomail $ecomail;

	#[Inject]
	public Container $container;

	public function actionDefault(): void
	{
		$this->template->className = get_class($this->ecomail);
	}
}