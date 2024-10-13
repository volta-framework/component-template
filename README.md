# Volta\Components\Templates

An HTML - PHP template module based on the PHP build in template engine.

```mermaid
%%{init: {'theme':'dark'}}%%
classDiagram
    class Volta_Component_Templates_Exception
    Exception<|--Volta_Component_Templates_Exception
    class Exception
    class Stringable {
         	&lt;&lt;interface&gt;&gt;
    }
    class Throwable {
         	&lt;&lt;interface&gt;&gt;
    }
    Stringable..|>Throwable
    Throwable..|>Exception
    class Volta_Component_Templates_NotFoundException
    Volta_Component_Templates_Exception<|--Volta_Component_Templates_NotFoundException
    class Volta_Component_Templates_Template {        
    }
   
   
  
    class Volta_Component_Templates_TemplateInterface {
         	&lt;&lt;interface&gt;&gt;       
    }
   
    Volta_Component_Templates_TemplateInterface..|>Volta_Component_Templates_Template
    class ArrayAccess {
         	&lt;&lt;interface&gt;&gt;
    }
    ArrayAccess..|>Volta_Component_Templates_Template
    Stringable..|>Volta_Component_Templates_Template
    class Volta_Component_Templates_View {
      
    }
    Volta_Component_Templates_Template<|--Volta_Component_Templates_View
```

## Usage

```php

use Volta\Component\Templates\Template as View

// Set the Base Directory globally
// Note: In Volta all directory references ends with a slash
View::setBaseDir('/path/to/templates/directory/');

// Create a view with basic placeholders
$view = new View('layout.html.php', [
    'title' => 'Unknown page'
]);

// add placeholders using the set function
$view->set('description', 'A simple home page')

// or use array access
$view['keywords'] = 'home, simple';

// add the template for the content and overwrite some off the parents
// placeholders
$view->addSubTemplate('content', 'content.html.php', ['title' => 'Contact'])

// render the view
echo $view;
```


`layout.html.php`
```php
<?php ?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $this->get('title', 'No Title'); ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <h1><?= $this['title']; ?></h1>
    <?= $this->getSubTemplate('content'); ?>
</body>
</html>
```

`content.html.php`
```php
<?php ?>
<h2><?= $this['title']; ?></h2>
```

