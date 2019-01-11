<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @link      https://github.com/juniwalk/tessa
 * @copyright (c) Martin Procházka
 * @license   MIT License
 */

namespace JuniWalk\Tessa\Exceptions;

final class UnableToDetectTypeException extends TessaException
{
    /**
     * @param  string  $file
     * @return static
     */
    public static function fromFile(string $file): self
    {
        return new static('Failed to detect asset type from file '.$file, 500);
    }
}
