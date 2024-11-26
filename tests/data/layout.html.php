<?php declare(strict_types=1);
use Volta\Component\Templates\Template;
use Volta\Component\Templates\TemplateInterface;
/** @var TemplateInterface $this */
?>
<!doctype html>
<html lang="`en`">
<head>
    <title><?= $this->get('title', 'Root Template')?></title>
    <style>
        .volta {
            color: darkred;
            background-color: rgba(255, 182, 193, 0.29);
            padding: 2px;
            font-size: 0.8rem;
            font-style: italic;
            font-family: "courier new", serif;
        }
        .volta.template-error:before {content: 'ERROR:'; font-weight: bold;}
        .volta.template-undefined:before {content: 'UNDEFINED: ';font-weight: bold;}


        footer {
            position:fixed;
            bottom:0;
            width:100%;
            padding: 20px;
            text-align: center;
        }

    </style>
</head>
<body>

   <?php $this->includeChild('header'); ?>
   <?php $this->includeChild('main'); ?>
   <?php $this->includeChild('footer'); ?>

</body>
</html>

