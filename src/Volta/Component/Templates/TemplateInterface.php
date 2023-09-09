<?php
/*
 * This file is part of the Volta package.
 *
 * (c) Rob Demmenie <rob@volta-server-framework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Volta\Component\Templates;

use Volta\Component\Templates\NotFoundException;
use Volta\Component\Templates\Template;
use Volta\Component\Templates\Exception as TemplatesException;

interface TemplateInterface
{

    #region - Constructor related methods:

    /**
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders Associative array with values to be replaced
     * @throws NotFoundException Thrown when the file is not found
     */
    public function __construct(string $file, array $placeholders = []);

    #endregion

    #region - Files related methods:

    /**
     * @return string The file containing the PHP and HTML
     */
    public function getFile(): string;

    #endregion

    #region - Nested Templates:

    /**
     * @see Template::addSubTemplate()
     * @return Template|null Reference to parent template
     */
    public function getParent(): ?Template;

    /**
     * @see Template::addSubTemplate()
     * @return Template[] A collection with references to its sub(child) templates
     */
    public function getSubTemplates(): array;

    /**
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders The collection of name - value pairs to be replaced in the template
     * @param string $index A (unique within the parent) name for this template
     * @return Template Reference to the current template instance, not the added sub template
     * @throws NotFoundException When the file can not be found
     */
    public function addSubTemplate(string $index, string $file, array $placeholders = [] ): self;

    /**
     * @param string $index A (unique within the parent) name for this templateA (unique within the parent) name for this template
     * @return Template Reference to the sub template instance
     * @throws NotFoundException When the sub tem[plate can not be found
     */
    public function getSubTemplate(string $index): Template;

    /**
     * @param string $index A (unique within the parent) name for this template
     * @return bool TRUE when exists, FALSE otherwise
     */
    public function hasSubTemplate(string $index): bool;

    /**
     * Includes a independent template
     *
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders The collection of name - value pairs to be replaced in the template
     * @throws NotFoundException When the file can not be found
     */
    public function include(string $file, array $placeholders = []) : void;

    #endregion:

    #region - Placeholders related methods:

    /**
     * @return array<string, mixed> The collection of name - value pairs to be replaced in the template
     */
    public function getPlaceholders(): array;

    /**
     * Checks if there is a placeholder with the name __$key__ and returns it
     * if not checks the default value __$default__ provided to be returned.
     * if no default value return 'NO VALUE FOR  "' .$key . '"'
     *
     * @param string $key The name of the placeholder
     * @param string|null $default The default value for the placeholder in case the placeholder is not set
     * @return mixed The value for the placeholder
     */
    public function get(string $key, mixed $default=null): mixed;

    /**
     * @param string $key The name of the placeholder
     * @param mixed $value The value for the placeholder
     * @return self A reference to the current template
     */
    public function set(string $key, mixed $value): self;

    /**
     * @param string $key
     * @return bool TRUE when a placeholder with the name __$key__ exists, FALSE otherwise
     */
    public function has(string $key): bool;

    #endregion

    #region - Render related methods:

    /**
     * Renders the __$file__, substitutes the __$placeholders__ and returns the result
     *
     * @see Template::getContent();
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders The collection of name - value pairs to be replaced in the template
     * @return string The rendered HTML
     * @throws NotFoundException When the file can not be found
     */
    public static function render(string $file, array $placeholders = []): string;

    /**
     * Renders the template, substitutes the __$placeholders__ and returns the result
     *
     * NOTE: The passed placeholders will be merged with the ones passed at construction and overwrites
     * existing placeholders. The parent placeholders, if any, will also be extracted but overwritten by
     * the current placeholders list
     *
     * @param array<string, mixed> $placeholders The collection of name - value pairs to be replaced in the template
     * @return string The rendered HTML
     */
    public function getContent(array $placeholders = []): string;

    #endregion

}