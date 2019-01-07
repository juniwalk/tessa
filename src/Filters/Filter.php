<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Filters;

use JuniWalk\Tessa\Assets\Asset;

interface Filter
{
    /**
     * @param  string  $content
     * @param  Asset  $asset
     * @return string
     */
    public function apply(string $content, Asset $asset): string;
}
