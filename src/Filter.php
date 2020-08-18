<?php declare(strict_types=1);

/**
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa;

interface Filter
{
    /**
     * @param  string  $content
     * @param  Asset  $asset
     * @return string
     */
    public function apply(string $content, Asset $asset): string;
}
