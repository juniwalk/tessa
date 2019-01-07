<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Bundles;

use JuniWalk\Tessa\Assets\HttpAsset;

final class AssetBundle extends AbstractBundle
{
    /** @var bool */
    protected $joinFiles = FALSE;


	/**
	 * @param  bool  $joinFiles
	 * @return void
	 */
	public function setJoinFiles(bool $joinFiles = TRUE): void
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
     * @param  string  $type
     * @return Bundle
     */
    public function getCombinedBy(string $type): Bundle
    {
        $bundle = new AssetBundle($this->getName());
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
