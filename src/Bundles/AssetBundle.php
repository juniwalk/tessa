<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets\FileAsset;
use JuniWalk\Tessa\Assets\HttpAsset;
use JuniWalk\Tessa\Assets\ScssAsset;
use JuniWalk\Tessa\Bundle;
use JuniWalk\Tessa\Exceptions\AssetTypeException;

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


	public function getCombinedBy(string $type): Bundle
	{
		$bundle = new AssetBundle($this->name);
		$bundle->setAttributes($this->attributes);
		$assets = [];

		foreach ($this->getAssets($type) as $asset) {
			if ($asset instanceof HttpAsset) {
				$bundle->addAsset($asset);
				continue;
			}

			$assets[] = $asset;
		}

		$bundle->addAsset(new CombinedBundle($this->name.'.'.$type, ...$assets));
		return $bundle;
	}


	/**
	 * @throws AssetTypeException
	 */
	public function addAssetFrom(string $file): void
	{
		// TODO: Detect type using schema? Example: js://, mjs://, css://
		// TODO: Remote files will have double schema: mjs://https://cdn.io/jquery-v4.0.0.js
		$params = $this->parseParams($file);
		$type = $params['type'] ?? null;

		if (!is_string($type)) {
			$type = null;
		}

		$this->assets[] = match (true) {
			ScssAsset::match($file) => new ScssAsset($file, $type),
			HttpAsset::match($file) => new HttpAsset($file, $type),
			FileAsset::match($file) => new FileAsset($file, $type),

			default => throw AssetTypeException::fromFile($file),
		};
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
