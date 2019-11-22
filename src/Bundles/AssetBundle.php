<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets\HttpAsset;

final class AssetBundle extends AbstractBundle
{
    /** @var bool */
    private $joinFiles = false;

    /** @var bool */
    private $defer = false;


	/**
	 * @param  bool  $joinFiles
	 * @return void
	 */
	public function setJoinFiles(bool $joinFiles = true): void
	{
		$this->joinFiles = $joinFiles;
	}


	/**
	 * @return bool
	 */
	public function isJoinFiles(): bool
	{
		return $this->joinFiles;
	}


	/**
	 * @param  bool  $defer
	 * @return void
	 */
	public function setDeferred(bool $defer = true): void
	{
		$this->defer = $defer;
	}


	/**
	 * @return bool
	 */
	public function isDeferred(): bool
	{
		return $this->defer;
	}


    /**
     * @param  string  $type
     * @return Bundle
     */
    public function getCombinedBy(string $type): Bundle
    {
        $bundle = new AssetBundle($this->getName());
		$bundle->setDeferred($this->defer);
		$name = $this->getName().'.'.$type;
        $assets = [];

        foreach ($this->assets as $asset) {
            if (!$asset->isTypeOf($type)) {
                continue;
            }

            if ($asset instanceof HttpAsset) {
                $bundle->addAsset($asset);
                continue;
            }

            $assets[] = $asset;
        }

        $bundle->addAsset(new CombinedBundle($name, ... $assets));
        return $bundle;
    }
}
