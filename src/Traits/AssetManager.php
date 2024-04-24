<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Traits;

use JuniWalk\Tessa\TessaRenderer;

trait AssetManager
{
	private TessaRenderer $tessaRenderer;

	public function injectTessaRenderer(TessaRenderer $tessaRenderer): void
	{
		$this->tessaRenderer = $tessaRenderer;
		// $this->onRender[] = fn() => $this->getTemplate()
		// 	->add('darkMode', $this->isDarkMode());
	}


	// public function isDarkMode(): bool
	// {
	// 	return (bool) $this->getHttpRequest()->getCookie('darkMode');
	// }


	protected function createComponentTessa(): TessaRenderer
	{
		return $this->tessaRenderer;
	}
}
