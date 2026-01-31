<?php
namespace Itgalaxy\Wc\Exchange1c\Admin;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

class MetaBox
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
            add_action('add_meta_boxes', [$this, 'addId1cBox']);
        }
    }

    public function addId1cBox()
    {
        add_meta_box(
            'id_1c',
            esc_html__('1C ID', 'itgalaxy-woocommerce-1c'),
            [$this, 'id1cShow'],
            'product',
            'side',
            'high'
        );
    }

    public function id1cShow($post)
    {
        if (!$post || !isset($post->ID)) {
            return;
        }

        echo esc_html(get_post_meta($post->ID, '_id_1c', true));
    }
}
