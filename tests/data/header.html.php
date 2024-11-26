<?php
use Volta\Component\Templates\TemplateInterface;
/**
 * @var TemplateInterface $this
 */
?>
<html lang="en">
<head>
    <title><?= $this->get('title', 'layouts default'); ?></title>
</head>
<body>
    <?php $this->getSubTemplate('content'); ?>
</body>
</html>
