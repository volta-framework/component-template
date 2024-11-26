<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;


class TemplateTest extends TestCase
{


    public function testView()
    {
        $view = new \Volta\Component\Templates\View('layout.html.php');
        $view->addSubTemplate('header', 'header.html.php');
        $view->addSubTemplate('main', 'main.html.php');
        $view->addSubTemplate('footer', 'footer.html.php');

        $result = $view->getContent();

        $this->assertEquals($result, '<h1>Hello World</h1>');

    }

}