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

class Template implements TemplateInterface, ArrayAccess
{

    #region - Construction:


    private string $file;

    protected string $name;

    private bool $verbose = false;

    private null|TemplateInterface $parent = null;

    /** @var array<string, mixed> */
    private array $placeholders = [];

    /** @var TemplateInterface[] */
    protected array $children= [];


    /**
     * @inheritDoc
     */
     public function __construct(string $file, array $placeholders = [])
     {
         $this->setFile($file);
         $this->name = substr(basename($file), 0  , strpos(basename($file), '.'));
         $this->placeholders = $placeholders;
     }

    /** @inheritDoc */
    public function getFile(): string
    {
        return $this->file;
    }

    /** @inheritDoc */
    public function getName(): string
    {
        if ($this->hasParent())  {
            return $this->getParent()->getName() . '/' . $this->name;
        }
        return $this->name;
    }

    /**
     * @param string $file
     * @return $this
     * @throws \Volta\Component\Templates\NotFoundException
     */
    protected function setFile(string $file): self
    {
        foreach(Settings::getBaseDirectories() as $dir) {
            if(file_exists($dir . $file)) {
                $this->file = $dir . $file;
                break;
            }
        }
        if(!isset($this->file)){
            throw new NotFoundException(sprintf('File "%s" not found!', $file));
        }
        return $this;
    }

    /** @inheritDoc */
    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    /** @inheritDoc */
    public function setVerbose(bool $verbose): self
    {
        $this->verbose = $verbose;
        return $this;
    }

    #endregion
    #region - Nested Templates:


    /** @inheritDoc */
    public function getParent(): TemplateInterface|null
    {
        return $this->parent;
    }

    /** @inheritDoc */
    public function hasParent(): bool
    {
        return !is_null($this->parent);
    }

    /** @inheritDoc */
    public function isRoot(): bool
    {
        return !$this->hasParent();
    }

    /** @inheritDoc */
    public function getChildren(): array
    {
        return $this->children;
    }

    /** @inheritDoc */
    public function addChild(string $templateName, TemplateInterface $template): self
    {
        if(isset($this->children[$templateName])){
            throw new TemplatesException(sprintf('Template "%s" already exists!', $templateName));
        }
        $template->parent = $this;
        $template->name = $templateName;
        $template->setVerbose($this->verbose);
        $this->children[$templateName] = $template;
        return $this;
    }

    /** @inheritDoc */
    public function addChildByFile(string $templateName, string $file, array $placeholders=[]): self
    {
        $template = new Template($file, $placeholders);
        $this->addChild($templateName, $template);
        return $this;
    }

    /** @inheritDoc */
    public function getChild(string $templateName): TemplateInterface
    {
        if(!$this->hasChild($templateName)) {
            throw new NotFoundException(sprintf('Child Template "%s" not found', $templateName));
        }
        return $this->children[$templateName];
    }

    /** @inheritDoc */
    public function hasChild(string $templateName): bool
    {
        return array_key_exists($templateName, $this->children);
    }

    /** @inheritDoc */
    public function removeChild(string $templateName): self
    {
        if(!$this->hasChild($templateName)) {
            throw new NotFoundException(sprintf('Child Template "%s" not found', $templateName));
        }
        unset($this->children[$templateName]);
        return $this;
    }


    #endregion:
    #region - Placeholders related methods:


    /** @inheritDoc */
    public function getPlaceholders(): array
    {
        $parentPlaceholders = [];
        if ($this->hasParent()) {
            $parentPlaceholders = $this->getParent()->getPlaceholders();
        }
        return array_merge($parentPlaceholders, $this->placeholders);
    }

    /** @inheritDoc */
    public function get(string $key, mixed $default=null): mixed
    {
        if (!$this->has($key)) {
            if ($this->hasParent()) return $this->getParent()->get($key, $default);
            if (null === $default) return '<span class="volta template-undefined">' .$key . '</span>';
            return $default;
        }
        return $this->placeholders[$key];
    }

    /** @inheritDoc */
    public function set(string $key, mixed $value): self
    {
        $this->placeholders[$key] = $value;
        return $this;
    }

    /** @inheritDoc */
    public function has(string $key): bool
    {
        return isset($this->placeholders[$key]);
    }

    /** @inheritDoc */
    public function unset(string $key): self
    {
        if ($this->has($key)) unset($this->placeholders[$key]);
        return $this;
    }


    #endregion
    #region - Render related methods:


    /** @inheritDoc */
    public function include(string $file, array $placeholders = []) : void
    {
        try{
            $template= new Template($file, $placeholders);
            echo $template;
        } catch(\Throwable $exception) {
            echo sprintf(PHP_EOL . '<div class="volta template-error">Template::include() failed: %s</div>' . PHP_EOL,  $exception->getMessage());
        }
    }

    /** @inheritDoc */
    public function includeChild(string $templateName, array $placeholders = []): void
    {
        try {
            $child = $this->getChild($templateName);
            echo $child->getContent($placeholders);
        } catch(\Throwable $exception) {
            echo sprintf(PHP_EOL . '<div class="volta template-error">Template::includeChild() failed: %s</div>' . PHP_EOL,  $exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getContent(array $placeholders = []): string
    {
        set_error_handler(
            function(int $code, string $message, string $file, int $line) {
                if (error_reporting() > 0 ) {
                    echo sprintf('<div class="volta template-error">Error(%d) in template(%s): "%s" on line %d</div>', $code, $this->getName(), $message, $line);
                }
                return false;
            }
        );

        $render = function($definedVariables) {
            ob_start();
            extract($definedVariables, EXTR_PREFIX_INVALID, '_');
            if ($this->getVerbose())  echo sprintf(PHP_EOL . '<!-- START: %s[%s] -->' . PHP_EOL,  basename($this->getFile()), $this->getName());
            include $this->getFile();
            if ($this->getVerbose()) echo sprintf(PHP_EOL . '<!-- END: %s[%s] -->' . PHP_EOL,  basename($this->getFile()), $this->getName());
            return PHP_EOL . ob_get_clean() . PHP_EOL;
        };

        $placeholdersOrg = $this->placeholders;
        $this->placeholders = array_merge($this->placeholders, $placeholders);

        $toBeExtracted = [];
        foreach($this->getPlaceholders() as $key => $value) {
            $toBeExtracted[preg_replace('/[^0-1a-zA-Z_]/', '_', $key)] = $value;
        }

        //$render = $render->bindTo($this);
        $html  = $render($toBeExtracted);
        restore_error_handler();
        $this->placeholders = $placeholdersOrg;
        return $html;

    }

    /**
     * @see Template::getContent();
     */
    public function __invoke(array $placeholders = []): string
    {
        return $this->getContent($placeholders);
    }

    /**
     * @see Template::getContent();
     */
    public function __toString(): string
    {
        return $this->getContent();
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
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->unset($offset);
    }


    #endregion

}