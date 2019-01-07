<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

use JuniWalk\Tessa\Bundles\Bundle;

final class ReadOnlyBundleException extends TessaException
{
    /**
     * @param  Bundle  $bundle
     * @return static
     */
    public static function fromBundle(Bundle $bundle): self
    {
        return new static('Bundle "'.$bundle->getName().'" is read-only and cannot be compiled.');
    }
}
