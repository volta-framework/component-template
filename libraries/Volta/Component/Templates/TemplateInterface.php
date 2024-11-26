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

    /**
     * @return string The file containing the PHP and HTML
     */
    public function getFile(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return bool
     */
    public function getVerbose(): bool;

    /**
     * @param bool $verbose
     * @return self
     */
    public function setVerbose(bool $verbose): self;


    #endregion
    #region - Nested Templates:


    /**
     * @see Template::addSubTemplate()
     * @return TemplateInterface|null Reference to parent template
     */
    public function getParent(): TemplateInterface|null;

    /**
     * @return bool
     */
    public function hasParent(): bool;

    /**
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * @see Template::addSubTemplate()
     * @return Template[] A collection with references to its sub(child) templates
     */
    public function getChildren(): array;

    /**
     * @param string $templateName A (unique within the parent) name for this template
     * @param TemplateInterface $template
     * @return TemplateInterface Reference to the current template instance, not the added sub template
     * @throws NotFoundException When the file can not be found
     * @throws TemplatesException When the templateName is already in use
     */
    public function addChild(string $templateName, TemplateInterface $template): self;

    /**
     * @param string $templateName A (unique within the parent) name for this template
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders The collection of name - value pairs to be replaced in the template     *
     * @return TemplateInterface Reference to the current template instance, not the added sub template
     * @throws NotFoundException When the file can not be found
     * @throws TemplatesException When the templateName is already in use
     */
    public function addChildByFile(string $templateName, string $file, array $placeholders=[]): self;

    /**
     * @param string $templateName A (unique within the parent) name for this template
     * @return TemplateInterface Reference to the sub template instance
     * @throws NotFoundException When the child template can not be found
     */
    public function getChild(string $templateName): TemplateInterface;

    /**
     * @param string $templateName A (unique within the parent) name for this template
     * @return bool TRUE when exists, FALSE otherwise
     */
    public function hasChild(string $templateName): bool;

    /**
     * @param string $templateName
     * @return self
     * @throws NotFoundException When the child template can not be found
     */
    public function removeChild(string $templateName): self;

    /**
     * Includes the content of an independent template into the buffer
     *
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders The collection of name - value pairs to be replaced in the template
     */
    public function include(string $file, array $placeholders = []) : void;

    /**
     * Includes the content of a child template into the buffer
     *
     * @param string $templateName
     * @param array $placeholders
     * @return void
     */
    public function includeChild(string $templateName, array $placeholders = []): void;


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

    /**
     * @param string $key
     * @return self
     */
    public function unset(string $key): self;


    #endregion
    #region - Render related methods:


    /**
     * NOTE: The passed placeholders temporarily overwrite the ones passed at construction or the ones of the
     * parent during the render process.
     *
     * @param array<string, mixed> $placeholders
     * @return string The rendered HTML
     */
    public function getContent(array $placeholders = []): string;


    #endregion

}