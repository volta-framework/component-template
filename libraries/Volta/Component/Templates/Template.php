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

use ArrayAccess;

use Volta\Component\Templates\NotFoundException as NotFoundException;
use Volta\Component\Templates\Exception as TemplatesException;


/**
 * Includes a PHP template file but in a local scope.
 *
 * @implements ArrayAccess<string, mixed>
 */
class Template implements TemplateInterface, ArrayAccess
{

    #region - Constructor related methods:

    /**
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders Associative array with values to be replaced
     * @throws NotFoundException Thrown when the file is not found
     */
     public function __construct(string $file, array $placeholders = [])
     {
         $this->setFile($file);
         $this->placeholders = $placeholders;
     }

    #endregion

    #region - Files related methods:


    /**
     * @var string internal storage of existing template file
     * @ignore Do not show up in generated documentation
     */
    private string $_file;

    /**
     * Returns the template location
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->_file;
    }

    /**
     * Sets the template location
     *
     * @param string $file
     * @return $this
     * @throws \Volta\Component\Templates\NotFoundException
     */
    public function setFile(string $file): self
    {
        foreach(Settings::getBaseDirectories() as $dir) {
            if(file_exists($dir . $file)) {
                $this->_file = $dir . $file;
                break;
            }
        }
        if(!isset($this->_file)){
            throw new NotFoundException(sprintf('Template "%s" not found!', $file));
        }
        return $this;
    }
    #endregion

    #region - Nested Templates:

    /**
     * @var Template|null  internal reference to parent template
     * @ignore Do not show up in generated documentation
     */
    protected ?Template $parent = null;

    /**
     * Getter
     *
     * Will be set when this template is included through the
     * Template::include() method. Defaults to null
     *
     * @return Template|null Reference to parent template
     */
    public function getParent(): ?Template
    {
        return $this->parent;
    }

    /**
     * @var Template[]  internal list with references to its sub templates
     * @ignore Do not show up in generated documentation
     */
    protected array $subTemplates = [];

    /**
     * Getter
     *
     * Will be filled when templates are included through the
     * Template::include() method.
     *
     * @return Template[] List with references to its child templates
     */
    public function getSubTemplates(): array
    {
        return $this->subTemplates;
    }

    /**
     * @param string $file
     * @param array<string, mixed> $placeholders
     * @param string $index
     * @return Template
     * @throws NotFoundException
     */
    public function addSubTemplate(string $index, string $file, array $placeholders = [] ): self
    {
        $template = new Template($file, $placeholders);
        $template->parent = $this;
        $this->subTemplates[$index] = $template;
        return $template;
    }

    /**
     * @param string|int $index
     * @return Template
     * @throws NotFoundException
     */
    public function getSubTemplate(string|int $index): self
    {
        if(!$this->hasSubTemplate($index)) {
            throw new NotFoundException(sprintf('SubTemplate "%s" not found', $index));
        }
        return $this->subTemplates[$index];
    }

    /**
     * @param string|int $index
     * @return bool
     */
    public function hasSubTemplate(string|int $index): bool
    {
        return array_key_exists($index, $this->subTemplates);
    }

    /**
     * Includes a independent template
     *
     * @param string $file
     * @param array<string, mixed> $placeholders
     * @throws NotFoundException
     */
    public function include(string $file, array $placeholders = []) : void
    {
        echo new Template($file, $placeholders);
    }

    #endregion:

    #region - Placeholders related methods:

    /**
     * @var array<string, mixed> Internal storage for the list of name - value pairs to be replaced in the template
     * @ignore Do not show up in generated documentation
     */
    private array $placeholders = [];

    /**
     * @return array<string, mixed> The list of name - value pairs to be replaced in the template
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }

    /**
     * @param array<string, mixed> $placeholders
     * @return self
     */
    public function setPlaceholders(array $placeholders): self
    {
        $this->placeholders = $placeholders;
        return $this;
    }

    /**
     * Checks if there is a placeholder with the name __$key__ and returns it
     * if not checks the default value __$default__ provided to be returned.
     * if no default value return 'NO VALUE FOR  "' .$key . '"'
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default=null): mixed
    {
        if (!$this->has($key)) {
            if(null === $default) {
                return 'NO VALUE FOR  "' .$key . '"';
            } else {
                return $default;
            }
        }
        return $this->placeholders[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, mixed $value): self
    {
        $this->placeholders[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->placeholders[$key]);
    }

    #endregion

    #region - Render related methods:

    /**
     * Creates a new template and returns rendered content
     *
     * @see Template::__invoke();
     * @see Template::getContent();
     *
     * @param string $file The file containing the PHP and HTML
     * @param array<string, mixed> $placeholders Associative array with values to be replaced
     * @return string The rendered HTML
     * @throws TemplatesException
     * @throws NotFoundException
     */
    public static function render(string $file, array $placeholders = []): string
    {
        $template = new static($file);
        return $template($placeholders);
    }

    /**
     * NOTE: The passed placeholders will be merged with the ones passed at construction and overwrites
     * existing placeholders
     *
     * @see Template::getContent();
     *
     * @param array<string, mixed> $placeholders Associative array with values to be replaced.
     * @return string
     * @throws TemplatesException
     */
    public function __invoke(array $placeholders = []): string
    {
        return $this->getContent($placeholders);
    }

    /**
     * @var array<string, mixed>
     */
    protected array $placeholdersOrg = [];

    /**
     * Renders the template, substitutes the __$placeholders__ and returns the result
     *
     * NOTE: The passed placeholders will be merged with the ones passed at construction and overwrites
     * existing placeholders. If we have a parent placeholders will also be extracted but overwritten by
     * the current placeholders list
     *
     * @param array<string, mixed> $placeholders
     * @return string
     * @throws Exception
     */
    public function getContent(array $placeholders = []): string
    {
        // start with an empty array of placeholders
        $currentPlaceholders = [];

        // if we have a parent set the parent placeholders as a starting point
        if ($this->getParent()) {
            $currentPlaceholders = array_merge($currentPlaceholders, $this->getParent()->getPlaceholders());
        }

        // override / merge with current placeholders already set
        $currentPlaceholders = array_merge($currentPlaceholders, $this->placeholders);

        // override / merge placeholders with passed placeholders
        $currentPlaceholders = array_merge($currentPlaceholders, $placeholders);

        // extract current placeholders
        $nr = extract($currentPlaceholders);

        // save original placeholders this way we can render the template multiple times with different values
        // keeping the original values intact and still use the get() method in the templates.
        $this->placeholdersOrg = $this->placeholders;
        $this->placeholders = $currentPlaceholders;

        // return value defaults to empty string;
        $html = '';

        // temporary get all errors to give some meaningfully template related feedback
        set_error_handler(
            /**
             * @throws TemplatesException
             */
            function(int $errno, string $errstr, string $errfile, int $errline)
            {
                if (error_reporting() > 0 ) {
                    throw new TemplatesException(
                        sprintf(
                            'Error %d in template(%s): "%s" on line %d',
                            $errno,
                             $this->getFile()
                            ,
                            $errstr,
                            $errline
                        )
                    );
                } else {
                    return false;
                }
            }
        );
        ob_start();
        echo PHP_EOL . '<!-- START: "' . basename($this->getFile()) . '" -->' . PHP_EOL;
        include $this->getFile();
        $html = ob_get_contents();
        $html .= PHP_EOL . '<!-- END: "' . basename($this->getFile()) . '" -->' . PHP_EOL;
        ob_end_clean();

        // restore error handler and placeholders
        restore_error_handler();
        $this->placeholders = $this->placeholdersOrg;

        return (string) $html;

    } // GetContent

    /**
     * @return string
     * @throws TemplatesException
     */
    public function __toString(): string
    {
        return $this->getContent( );
    }

    #endregion

    #region - ArrayAccess stubs:

    /**
     * @inheritdoc
     * @see https://www.php.net/manual/en/arrayaccess.offsetExists.php
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    /**
     * @inheritdoc
     * @see https://www.php.net/manual/en/arrayaccess.offsetGet.php
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     * @see https://www.php.net/manual/en/arrayaccess.offsetSet.php
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritdoc
     * @see https://www.php.net/manual/en/arrayaccess.offsetUnset.php
     * @throws TemplatesException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new TemplatesException('Changing the placeholders through array access not allowed');
    }

    #endregion


} // class