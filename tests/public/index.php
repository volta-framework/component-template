<?php
declare(strict_types=1);

use Volta\Component\Templates\Template;

require  __DIR__ . '/../bootstrap.php';


try {
    $rootTemplate = new Template('layout.html.php',
        [
            'root-placeholder1' => 'root placeholder1 value',
            'title' => 'layout'
        ]
    );
    $rootTemplate->setVerbose(true);


    $headerTemplate = new Template('header.html.php', ['title' => 'header']);
    $headerPartial = new Template('partial.html.php',  ['title' => 'partial']);

    $headerTemplate->addChild('partial', $headerPartial);

    $rootTemplate->addChild('header', $headerTemplate);
    $rootTemplate->addChild('main', new Template('main.html.php', ['title' => 'Main']));
    $rootTemplate->addChild('footer', new Template('footer.html.php', ['title' => 'Footer']));


    echo $rootTemplate;
} catch (\Throwable $e) {

    header('Content-Type: text/plain');
    print_r($e);
}