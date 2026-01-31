<?php
use Itgalaxy\Wc\Exchange1c\Admin\SettingsPage;
use Itgalaxy\Wc\Exchange1c\Admin\MetaBox;
use Itgalaxy\Wc\Exchange1c\Admin\ProductTableColumn;
use Itgalaxy\Wc\Exchange1c\Admin\ProductCatTableColumn;

if (!defined('ABSPATH')) {
    exit();
}

// do not continue initialization if not admin panel
if (!is_admin()) {
    return;
}

SettingsPage::getInstance();
MetaBox::getInstance();
ProductTableColumn::getInstance();
ProductCatTableColumn::getInstance();
