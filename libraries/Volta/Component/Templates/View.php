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

use Volta\Component\Templates\Template as BaseTemplate;

/**
 * Combines a layout and content templates
 */
class View extends BaseTemplate
{

    public function __construct(string $layout, array $placeholders = [])
    {
        parent::__construct($layout, $placeholders);
    }

}