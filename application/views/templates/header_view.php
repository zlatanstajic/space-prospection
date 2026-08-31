<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=html_escape($meta_description)?>">
    <meta name="author" content="Zlatan Stajić">
    <meta name="theme-color" content="#620031">
    <link rel="apple-touch-icon" sizes="33x32" href="<?=base_url('assets/images/favicon.png')?>">
    <link rel="icon" href="<?=base_url('assets/images/favicon.png')?>" type="image/png">
    <title><?=html_escape($page_title)?> | Space Prospection</title>
    <link rel="stylesheet" href="<?=base_url('assets/css/style.css')?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/mobile.css')?>">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>
<div id="page">
<header id="header">
    <div>
        <a href="<?=base_url()?>" class="logo" aria-label="Space Prospection home">
            <img src="<?=base_url('assets/images/logo.png')?>" alt="Space Prospection">
        </a>
        <ul id="navigation">
            <?php foreach ($navigation as $segment): ?>
                <li<?=$segment['link'] === $current_page ? ' class="selected"' : ''?>>
                    <a href="<?=base_url($segment['link'])?>">
                        <?=html_escape($segment['name'])?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</header>
<main id="main-content">
