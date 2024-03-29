<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets;

final class AssetBundle extends AbstractBundle
{
	private bool $joinFiles = false;
	private bool $defer = false;
	private bool $async = false;


	public function setJoinFiles(bool $joinFiles = true): void
	{
		$this->joinFiles = $joinFiles;
	}


	public function isJoinFiles(): bool
	{
		return $this->joinFiles;
	}


	public function setDeferred(bool $defer = true): void
	{
		$this->defer = $defer;
	}


	public function isDeferred(): bool
	{
		return $this->defer;
	}


	public function setAsync(bool $async = true): void
	{
		$this->async = $async;
	}


	public function isAsync(): bool
	{
		return $this->async;
	}


	public function discoverAsset(string $file): void
	{
		$params = $this->parseParams($file);
		$type = $params['type'] ?? null;

		switch(true) {
			case Assets\HttpAsset::match($file):
				$asset = new Assets\HttpAsset($file, $type);
				break;

			case Assets\ScssAsset::match($file):
				$asset = new Assets\ScssAsset($file, $type);
				break;

			default:
				$asset = new Assets\FileAsset($file, $type);
				break;
		}

		$this->assets[] = $asset;
	}


	public function getCombinedBy(string $type): Bundle
	{
		$bundle = new AssetBundle($this->name);
		$bundle->setCookieConsent($this->cookieConsent);
		$bundle->setDeferred($this->defer);
		$bundle->setAsync($this->async);
		$name = $this->name.'.'.$type;
		$assets = [];

		foreach ($this->assets as $asset) {
			if (!$asset->isTypeOf($type)) {
				continue;
			}

			if ($asset instanceof Assets\HttpAsset) {
				$bundle->addAsset($asset);
				continue;
			}

			$assets[] = $asset;
		}

		$bundle->addAsset(new CombinedBundle($name, ... $assets));
		return $bundle;
	}


	private function parseParams(string $file): array
	{
		if (!$query = parse_url($file, PHP_URL_QUERY)) {
			return [];
		}

		parse_str($query, $params);
		return $params;
	}
}
