<?php declare(strict_types=1); ?>
<header>
    <h1><?= $title ?? 'This is the header Title'; ?></h1>
    <?php $this->includeChild('partial', ['title' => 'HEADER ARTICLE']); ?>
    <?php $this->includeChild('partial', ['title' => 'HEADER ARTICLE']); ?>
</header>