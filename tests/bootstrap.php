<?php declare(strict_types = 1);



require_once __DIR__ . '/../libraries/Volta/Component/Templates/Exception.php';
require_once __DIR__ . '/../libraries/Volta/Component/Templates/NotFoundException.php';
require_once __DIR__ . '/../libraries/Volta/Component/Templates/Settings.php';
require_once __DIR__ . '/../libraries/Volta/Component/Templates/TemplateInterface.php';
require_once __DIR__ . '/../libraries/Volta/Component/Templates/Template.php';


\Volta\Component\Templates\Settings::setBaseDirectories([__DIR__ . '/data/']);