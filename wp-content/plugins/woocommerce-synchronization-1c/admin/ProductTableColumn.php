<?php
namespace Itgalaxy\Wc\Exchange1c\Admin;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

class ProductTableColumn
{
    private static $instance = false;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!empty($settings['show_1c_code_metabox'])) {
            add_action('manage_product_posts_custom_column', [$this, 'add1cToNameValue'], 11, 2);
        }
    }

    public function add1cToNameValue($columnName, $postID)
    {
        if ($columnName === 'name') {
            echo '<br>'
                . esc_html(get_post_meta($postID, '_id_1c', true));
        }
    }
}
