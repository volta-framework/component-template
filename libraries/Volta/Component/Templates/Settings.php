<?php
/*
 * This file is part of the Volta package.
 *
 * (c) Rob Demmenie <rob@volta-framework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Volta\Component\Templates;

use Volta\Component\Templates\NotFoundException as NotFoundException;

class Settings
{

    /**
     * @var array<string> internal storage of base directories
     * @ignore Do not show up in generated documentation
     */
    private static array $_baseDirectories = [];

    /**
     * @return array<string> The directory the templates are loaded from
     */
    public static function getBaseDirectories(): array
    {
        return Settings::$_baseDirectories;
    }

    /**
     * Sets the directories the templates are loaded from
     *
     * @param array $baseDirectories
     * @return void
     * @throws NotFoundException
     */
    public static function setBaseDirectories(array $baseDirectories): void
    {
        foreach($baseDirectories as $index => $baseDirectory) {

            // validate path
            $realBaseDirectory = realpath($baseDirectory);
            if($realBaseDirectory === false || !is_dir($realBaseDirectory)) {
                throw new NotFoundException(sprintf(__METHOD__ . ': Templates base directory "%s" not found!', $baseDirectory));
            }

            // always add directory separators when storing directory names
            $realBaseDirectory .= DIRECTORY_SEPARATOR;

            // do not allow doubles
            if(in_array($realBaseDirectory , Settings::$_baseDirectories)) continue;

            // finally add to the list
            Settings::$_baseDirectories[$index] = $realBaseDirectory;
        }
    }
}