<?php
namespace Itgalaxy\Wc\Exchange1c\Includes;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Filters\FindProductId;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Filters\SkipProductByXml;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\RootProcessStarter;

class Bootstrap
{
    const OPTIONS_KEY = 'wc-itgalaxy-1c-exchange-settings';
    const PURCHASE_CODE_OPTIONS_KEY = 'wc-itgalaxy-1c-exchange-purchase-code';

    // real names of XML tags
    const XML_TAGS = [
        'id' => 'Ид',
        'sku' => 'Артикул',
        'weight' => 'Вес',
        'options' => 'Свойства',
        'option' => 'Свойство',
        'name' => 'Наименование',
        'priceTypes' => 'ТипыЦен',
        'priceType' => 'ТипЦены',
        'stock' => 'Количество',
        'value' => 'Значение',
    ];

    public static $plugin = '';

    private static $instance = false;

    protected function __construct($file)
    {
        self::$plugin = $file;

        self::pluginLifeCycleActionsRegister();

        // bind cron actions
        Cron::getInstance();

        // processing request from the accounting system
        add_action('init', function () {
            // check is exchange request
            if (!Helper::isExchangeRequest()) {
                return;
            }

            // bind filters
            FindProductId::getInstance();
            SkipProductByXml::getInstance();

            // exchange start
            RootProcessStarter::getInstance();
        });
    }

    public static function getInstance($file)
    {
        if (!self::$instance) {
            self::$instance = new self($file);
        }

        return self::$instance;
    }

    public static function pluginLifeCycleActionsRegister()
    {
        register_activation_hook(
            self::$plugin,
            ['Itgalaxy\Wc\Exchange1c\Includes\Bootstrap', 'pluginActivation']
        );
        register_deactivation_hook(
            self::$plugin,
            ['Itgalaxy\Wc\Exchange1c\Includes\Bootstrap', 'pluginDeactivation']
        );
        register_uninstall_hook(
            self::$plugin,
            ['Itgalaxy\Wc\Exchange1c\Includes\Bootstrap', 'pluginUninstall']
        );
    }

    public static function pluginActivation()
    {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            wp_die(
                esc_html__(
                    'To run the plug-in, you must first install and activate the WooCommerce plugin.',
                    'itgalaxy-woocommerce-1c'
                ),
                esc_html__(
                    'Error while activating the WooCommerce - 1C:Enterprise - Data Exchange',
                    'itgalaxy-woocommerce-1c'
                ),
                [
                    'back_link' => true
                ]
            );
            // Escape ok
        }

        self::setRoleCapabilities();
        self::addWcAttributesTableColumn();
        self::copyRootEntryImportFile();

        // add option row in table for plugin settings
        if (get_option(self::OPTIONS_KEY) === false) {
            add_option(self::OPTIONS_KEY, [], '', 'no');
        }

        if (get_option(self::PURCHASE_CODE_OPTIONS_KEY) === false) {
            add_option(self::PURCHASE_CODE_OPTIONS_KEY, '', '', 'no');
        }

        if (get_option('all_prices_types') === false) {
            add_option('all_prices_types', [], '', 'no');
        }

        if (get_option('ITGALAXY_WC_1C_PLUGIN_VERSION') === false) {
            add_option('ITGALAXY_WC_1C_PLUGIN_VERSION', ITGALAXY_WC_1C_PLUGIN_VERSION, '', 'no');
        }
    }

    public static function pluginDeactivation()
    {
        // Nothing
    }

    public static function pluginUninstall()
    {
        // Nothing
    }

    private static function copyRootEntryImportFile()
    {
        if (!file_exists(ITGALAXY_WC_1C_PLUGIN_DIR . 'import-1c.php')) {
            return;
        }

        if (file_exists(ABSPATH . 'import-1c.php')) {
            return;
        }

        copy(ITGALAXY_WC_1C_PLUGIN_DIR . 'import-1c.php', ABSPATH . 'import-1c.php');
    }

    private static function addWcAttributesTableColumn()
    {
        global $wpdb;

        $dbName = DB_NAME;
        $columnExists = $wpdb->query(
            "SELECT * FROM information_schema.COLUMNS
                  WHERE TABLE_SCHEMA = '{$dbName}'
                  AND TABLE_NAME = '{$wpdb->prefix}woocommerce_attribute_taxonomies'
                  AND COLUMN_NAME = 'id_1c'"
        );

        if (!$columnExists) {
            $wpdb->query(
                "ALTER TABLE {$wpdb->prefix}woocommerce_attribute_taxonomies
                ADD id_1c varchar(200) NOT NULL"
            );
        }
    }

    private static function setRoleCapabilities()
    {
        $roles = new \WP_Roles();

        foreach (self::capabilities() as $capGroup) {
            foreach ($capGroup as $cap) {
                $roles->add_cap('administrator', $cap);

                if (is_multisite()) {
                    $roles->add_cap('super_admin', $cap);
                }
            }
        }
    }

    private static function capabilities()
    {
        $capabilities = [
            'core' => [
                'manage_' . self::OPTIONS_KEY
            ]
        ];

        flush_rewrite_rules(true);

        return $capabilities;
    }

    private function __clone()
    {
        // Nothing
    }
}
