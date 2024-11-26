<?php declare(strict_types=1);

use Volta\Component\Templates\Template;
use Volta\Component\Templates\TemplateInterface;
/** @var TemplateInterface $this */
?>
    <main>
        <h2><?= $title ?? '...'; ?></h2>
        <?php $this->include('partial.html.php', ['title' => 'MAIN ARTICLE']); ?>
        <?php $this->includeChild('partial', ['title' => 'MAIN ARTICLE']); ?>
    </main>