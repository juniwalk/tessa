<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets;
use JuniWalk\Tessa\Bundle;

final class AssetBundle extends AbstractBundle
{
	private bool $joinFiles = false;


	public function setJoinFiles(bool $joinFiles = true): void
	{
		$this->joinFiles = $joinFiles;
	}


	public function isJoinFiles(): bool
	{
		return $this->joinFiles;
	}


	public function addAssetFrom(string $file): void
	{
		$params = $this->parseParams($file);
		$type = $params['type'] ?? null;

		if (!is_string($type)) {
			$type = null;
		}

		$this->assets[] = match (true) {
			Assets\HttpAsset::match($file) => new Assets\HttpAsset($file, $type),
			Assets\ScssAsset::match($file) => new Assets\ScssAsset($file, $type),

			default => new Assets\FileAsset($file, $type),
		};
	}


	public function getCombinedBy(string $type): Bundle
	{
		$bundle = new AssetBundle($this->name);
		$bundle->setAttributes($this->attributes);
		$assets = [];

		foreach ($this->getAssets($type) as $asset) {
			if ($asset instanceof Assets\HttpAsset) {
				$bundle->addAsset($asset);
				continue;
			}

			$assets[] = $asset;
		}

		$bundle->addAsset(new CombinedBundle($this->name.'.'.$type, ...$assets));
		return $bundle;
	}


	/**
	 * @return array<int|string, mixed>
	 */
	private function parseParams(string $file): array
	{
		if (!$query = parse_url($file, PHP_URL_QUERY)) {
			return [];
		}

		parse_str($query, $params);
		return $params;
	}
}
