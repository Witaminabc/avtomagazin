<?php
namespace Itgalaxy\Wc\Exchange1c\Admin;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;

class ProductCatTableColumn
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
            add_filter('manage_edit-product_cat_columns', [$this, 'add1cValueColumn'], 10, 3);
            add_filter('manage_product_cat_custom_column', [$this, 'add1cValue'], 10, 3);
        }
    }

    public function add1cValueColumn($columns)
    {
        $columns['wc1c'] =  esc_html__('1C', 'itgalaxy-woocommerce-1c');

        return $columns;
    }

    public function add1cValue($columns, $column, $id)
    {
        if ($column === 'wc1c') {
            $columns .= esc_html(get_term_meta($id, '_id_1c', true));
        }

        return $columns;
    }
}
