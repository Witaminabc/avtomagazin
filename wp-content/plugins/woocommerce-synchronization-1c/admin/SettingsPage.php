<?php
namespace Itgalaxy\Wc\Exchange1c\Admin;

use Itgalaxy\Wc\Exchange1c\Includes\AssetsHelper;
use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\Includes\Helper;

class SettingsPage
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
        add_action('admin_menu', [$this, 'addSubmenu'], 1000); // 1000 - fix priority for Admin Menu Editor

        if (isset($_GET['page']) && $_GET['page'] === Bootstrap::OPTIONS_KEY) {
            add_action('admin_enqueue_scripts', function () {
                wp_enqueue_style(
                    'itgalaxy-woocommerce-1c-page-css',
                    AssetsHelper::getPathAssetFile('/admin/css/app.css'),
                    false,
                    null
                );
            });
        }
    }

    public function addSubmenu()
    {
        add_submenu_page(
            'woocommerce',
            esc_html__('1C Data Exchange', 'itgalaxy-woocommerce-1c'),
            esc_html__('1C Data Exchange', 'itgalaxy-woocommerce-1c'),
            'manage_woocommerce',
            Bootstrap::OPTIONS_KEY,
            [$this, 'page']
        );
    }

    public function page()
    {
        if (isset($_POST['option_page_synchronization_from_1c_hidden']) && $_POST['option_page_synchronization_from_1c_hidden'] == 1) {
            update_option('synchronization_user', strip_tags($_POST['synchronization_user']));
            update_option('synchronization_pass', strip_tags($_POST['synchronization_pass']));
            update_option('old_day_synchronization_logs', intval($_POST['old_day_synchronization_logs']));
            update_option('exclude_1c_categories', strip_tags($_POST['exclude_1c_categories']));
            update_option('synchronization_post_author', intval($_POST['synchronization_post_author']));

            if (!empty($_POST['empty_price_type_key'])) {
                $allPricesTypes = [];
                $allPricesTypes[$_POST['empty_price_type_key']] = $_POST['empty_price_type_name'];
                update_option('all_prices_types', $allPricesTypes);
                $_POST[Bootstrap::OPTIONS_KEY]['price_type_1'] = $_POST['empty_price_type_key'];
            }

            if (isset($_POST[Bootstrap::OPTIONS_KEY])) {
                update_option(Bootstrap::OPTIONS_KEY, $_POST[Bootstrap::OPTIONS_KEY]);
            } else {
                update_option(Bootstrap::OPTIONS_KEY, []);
            }
            ?>
            <div class="updated">
                <p>
                    <strong>
                        <?php esc_html_e('Settings have been saved.', 'itgalaxy-woocommerce-1c'); ?>
                    </strong>
                </p>
            </div>
            <?php
            wp_redirect($_SERVER['REQUEST_URI']);
        }

        //check extensions end show notices
        $this->extensionNotices();

        $exclude1cCategories = get_option('exclude_1c_categories');
        $synchronizationPostAuthor = get_option('synchronization_post_author');

        $oldDaySynchronizationLogs = intval(get_option('old_day_synchronization_logs'));
        if ($oldDaySynchronizationLogs <= 1) {
            $oldDaySynchronizationLogs = 30;
        }

        $allPricesTypes = get_option('all_prices_types');
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        ?>
        <div id="poststuff" class="wrap woocommerce">
            <h1><?php esc_html_e('Sync settings with 1C', 'itgalaxy-woocommerce-1c'); ?></h1>
            <?php
            echo sprintf(
                '%1$s <a href="%2$s" target="_blank">%3$s</a>. %4$s.',
                esc_html__('Plugin documentation: ', 'itgalaxy-woocommerce-1c'),
                esc_url(ITGALAXY_WC_1C_PLUGIN_URL . 'documentation/index.html#step-1'),
                esc_html__('open', 'itgalaxy-woocommerce-1c'),
                esc_html__('Or open the folder `documentation` in the plugin and open index.html', 'itgalaxy-woocommerce-1c')
            );
            ?>
            <hr>
            <form method="post" action="#">
                <input type="hidden" name="option_page_synchronization_from_1c_hidden" value="1">
                <?php
                $this->tempCatalogInfo();
                $this->auth1cInfo();
                ?>
                <div class="postbox wc1c-padding">
                    <h3 class="hndle">
                        <?php esc_html_e('Additionally', 'itgalaxy-woocommerce-1c'); ?>
                    </h3>
                    <div class="inside">
                        <?php
                        self::showCheckbox(
                            [
                                'title' => esc_html__('Enable exchange', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If disabled, no exchange will be possible.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'enable_exchange',
                            $settings
                        );
                        ?>
                        <hr>
                        <div class="form-group">
                            <label>
                                <?php esc_html_e('Product / Image Owner', 'itgalaxy-woocommerce-1c'); ?>
                            </label>
                            <select class="form-control input-sm" name="synchronization_post_author">
                                <?php
                                foreach (get_users(['role' => 'administrator']) as $user) {
                                    echo '<option '
                                        . ($synchronizationPostAuthor == $user->ID ? 'selected' : '')
                                        . ' value="' . (int) $user->ID . '">'
                                        . esc_html($user->user_login)
                                        . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <hr>
                        <?php
                        self::showInput(
                            [
                                'title' => esc_html__('File part size:', 'itgalaxy-woocommerce-1c'),
                                'type' => 'number',
                                'description' => esc_html__(
                                    'The maximum size of the part of the exchange files transmitted from 1C (in bytes).',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'default' => 1000000
                            ],
                            'file_limit',
                            $settings
                        );
                        ?>
                        <hr>
                        <?php
                        self::showInput(
                            [
                                'title' => esc_html__('Script running time (second):', 'itgalaxy-woocommerce-1c'),
                                'type' => 'number',
                                'description' => esc_html__(
                                    'Maximum time the sync script runs (in seconds).',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'default' => 20
                            ],
                            'time_limit',
                            $settings
                        );
                        ?>
                        <hr>
                        <?php
                        self::showCheckbox(
                            [
                                'title' => esc_html__('Exchange in the archive', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If enabled, the exchange takes place through a zip archive.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'use_file_zip',
                            $settings
                        );
                        ?>
                        <hr>
                        <?php
                        self::showCheckbox(
                            [
                                'title' => esc_html__('Remove missing products (full unload)', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If enabled, all products that are missing in the unloading will be deleted.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'remove_missing_products',
                            $settings
                        );
                        ?>
                        <div class="form-group">
                            <label>
                                <?php esc_html_e('Categories for exclusion:', 'itgalaxy-woocommerce-1c'); ?>
                            </label>
                            <textarea class="large-text"
                                name="exclude_1c_categories"><?php echo esc_attr($exclude1cCategories); ?></textarea>
                            <p class="description">
                                <?php esc_html_e('Category IDs with a semicolon.', 'itgalaxy-woocommerce-1c'); ?>
                            </p>
                        </div>
                        <?php
                        self::showCheckbox(
                            [
                                'title' => esc_html__(
                                    'Set category thumbnails automatically (based on a product with a miniature in the '
                                        . 'category)',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => ''
                            ],
                            'set_category_thumbnail_by_product',
                            $settings
                        );
                        ?>
                    </div>
                </div>
                <div class="postbox wc1c-padding">
                    <h3 class="hndle">
                        <?php esc_html_e('For prices', 'itgalaxy-woocommerce-1c'); ?>
                    </h3>
                    <div class="inside">
                        <?php
                        self::showSelect(
                            [
                                'title' => esc_html__('Price processing type', 'itgalaxy-woocommerce-1c'),
                                'options' => apply_filters(
                                    'itglx_wc1c_price_work_rules',
                                    [
                                        'regular' => esc_html__(
                                            'Mode - 1: Only the base price is set (Price Type 1)',
                                            'itgalaxy-woocommerce-1c'
                                        ),
                                        'regular_and_sale' => esc_html__(
                                            'Mode - 2: The base and sale price are set (Price Type 1 - basic, Price '
                                                . 'Type 2 - sale)',
                                            'itgalaxy-woocommerce-1c'
                                        ),
                                        'regular_and_show_list' => esc_html__(
                                            'Mode - 3: Only the base price is set (Price Type 1) and show the price '
                                                . 'list in the product page',
                                            'itgalaxy-woocommerce-1c'
                                        ),
                                        'regular_and_show_list_and_apply_price_depend_cart_totals' => esc_html__(
                                            'Mode - 4: Only the base price is set (Price Type 1) and show the price '
                                                . 'list in the product page and apply price types depending on the '
                                                . 'amount in the cart',
                                            'itgalaxy-woocommerce-1c'
                                        ),
                                    ]
                                ),
                                'description' => esc_html__(
                                    'Choose the rule of working with prices that is convenient for you.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'price_work_rule',
                            $settings
                        );

                        $priceTypes = [];

                        if (is_array($allPricesTypes)) {
                            foreach ($allPricesTypes as $key => $name) {
                                $priceTypes[$key] = is_array($name) ? $name['name'] : $name;
                            }
                        }
                        ?>
                        <hr>
                        <?php
                        self::showCheckbox(
                            [
                                'title' => esc_html__(
                                    'Delete sale price (works for - Mode 1)',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => ''
                            ],
                            'remove_sale_price',
                            $settings
                        );
                        ?>
                        <hr>
                        <?php
                        if (!empty($priceTypes)) {
                            echo '<table><tr>';
                            echo '<td class="wc1c-settings-td">';
                            self::showSelect(
                                [
                                    'title' => esc_html__('Price Type 1', 'itgalaxy-woocommerce-1c'),
                                    'options' => $priceTypes
                                ],
                                'price_type_1',
                                $settings
                            );

                            echo '</td>';
                            echo '<td class="wc1c-settings-td">';

                            self::showInput(
                                [
                                    'title' => esc_html__('Caption:', 'itgalaxy-woocommerce-1c'),
                                    'type' => 'text',
                                    'description' => esc_html__(
                                        'Used for mode 3.',
                                        'itgalaxy-woocommerce-1c'
                                    )
                                ],
                                'price_type_1_text',
                                $settings
                            );

                            echo '</td>';
                            echo '</tr></table>';
                        } else {
                            ?>
                            <div class="form-group">
                                <label>
                                    <?php esc_html_e('Price Type 1', 'itgalaxy-woocommerce-1c'); ?>
                                </label>
                                <input type="text"
                                    class="form-control input-sm"
                                    name="empty_price_type_key"
                                    placeholder="<?php esc_attr_e('Price Type Code', 'itgalaxy-woocommerce-1c'); ?>">
                                <input type="text"
                                    class="form-control input-sm"
                                    name="empty_price_type_name"
                                    placeholder="<?php esc_attr_e('Price Type Name', 'itgalaxy-woocommerce-1c'); ?>">
                            </div>
                            <?php
                        }
                        ?>
                        <hr>
                        <?php
                        if (!empty($priceTypes)) {
                            $priceTypesOptions = ['' => esc_html__('Not chosen', 'itgalaxy-woocommerce-1c')] + $priceTypes;

                            echo '<table><tr>';
                            echo '<td class="wc1c-settings-td">';
                            self::showSelect(
                                [
                                    'title' => esc_html__('Price Type 2', 'itgalaxy-woocommerce-1c'),
                                    'options' => $priceTypesOptions
                                ],
                                'price_type_2',
                                $settings
                            );

                            echo '</td>';
                            echo '<td class="wc1c-settings-td">';

                            self::showInput(
                                [
                                    'title' => esc_html__('Caption:', 'itgalaxy-woocommerce-1c'),
                                    'type' => 'text',
                                    'description' => esc_html__(
                                        'Used for mode 3.',
                                        'itgalaxy-woocommerce-1c'
                                    )
                                ],
                                'price_type_2_text',
                                $settings
                            );

                            echo '</td>';
                            echo '<td class="wc1c-settings-td">';

                            self::showInput(
                                [
                                    'title' => esc_html__('Cart totals:', 'itgalaxy-woocommerce-1c'),
                                    'type' => 'number',
                                    'description' => esc_html__(
                                        'Used for mode 4. Use price type if there is more in the cart.',
                                        'itgalaxy-woocommerce-1c'
                                    )
                                ],
                                'price_type_2_summ',
                                $settings
                            );

                            echo '</td>';
                            echo '</tr></table>';

                            if (count($priceTypes) > 2) {
                                for ($i = 3; $i <= count($priceTypes); $i++) {
                                    echo '<hr>';
                                    echo '<table><tr>';
                                    echo '<td class="wc1c-settings-td">';

                                    self::showSelect(
                                        [
                                            'title' => esc_html__('Price Type', 'itgalaxy-woocommerce-1c') . ' ' . $i,
                                            'options' => $priceTypesOptions
                                        ],
                                        'price_type_' . $i,
                                        $settings
                                    );

                                    echo '</td>';
                                    echo '<td class="wc1c-settings-td">';

                                    self::showInput(
                                        [
                                            'title' => esc_html__('Caption:', 'itgalaxy-woocommerce-1c'),
                                            'type' => 'text',
                                            'description' => esc_html__(
                                                'Used for mode 3.',
                                                'itgalaxy-woocommerce-1c'
                                            )
                                        ],
                                        'price_type_'  . $i . '_text',
                                        $settings
                                    );

                                    echo '</td>';
                                    echo '<td class="wc1c-settings-td">';

                                    self::showInput(
                                        [
                                            'title' => esc_html__('Cart totals:', 'itgalaxy-woocommerce-1c'),
                                            'type' => 'number',
                                            'description' => esc_html__(
                                                'Used for mode 4. Use price type if there is more in the cart.',
                                                'itgalaxy-woocommerce-1c'
                                            )
                                        ],
                                        'price_type_'  . $i . '_summ',
                                        $settings
                                    );

                                    echo '</td>';
                                    echo '</tr></table>';
                                }
                            }
                        } else {
                            ?>
                            <div class="form-group">
                                <label>
                                    <?php esc_html_e('Price Type 2', 'itgalaxy-woocommerce-1c'); ?>
                                </label>
                                <p class="description">
                                    <?php
                                    esc_html_e(
                                        'The selection will be available after the first exchange.',
                                        'itgalaxy-woocommerce-1c'
                                    );
                                    ?>
                                </p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                $fields = [
                    [
                        'title' => esc_html__('For products', 'itgalaxy-woocommerce-1c'),
                        'fields' => [
                            'find_product_by_sku' => [
                                'type' => 'checkbox',
                                'title' => esc_html__('Try to find a product by SKU', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If enabled, then the plugin tries to find the product by SKU, if it is not '
                                    . 'found by ID from 1C. It may be useful if the site already has products and, in '
                                    . 'order not to create everything again, you can make their first link by SKU.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'product_use_full_name' => [
                                'type' => 'checkbox',
                                'title' => esc_html__('Use full name', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If enabled, the title of the product will be recorded not "Name" and "Full Name" '
                                        . 'of the details of the products.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'products_stock_null_rule' => [
                                'type' => 'select',
                                'title' => esc_html__('Products with a stock <= 0:', 'itgalaxy-woocommerce-1c'),
                                'options' => [
                                    '0' => esc_html__(
                                        'Hide (not available for viewing and ordering)',
                                        'itgalaxy-woocommerce-1c'
                                    ),
                                    '1' => esc_html__(
                                        'Do not hide and give the opportunity to put in the basket',
                                        'itgalaxy-woocommerce-1c'
                                    ),
                                    '2' => esc_html__(
                                        'Do not hide, but do not give the opportunity to put in the basket',
                                        'itgalaxy-woocommerce-1c'
                                    )
                                ],
                                'description' => esc_html__(
                                    'Only products with a non-empty price can be opened.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'write_product_description_in_excerpt' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Write the "Description" in a short description of the product.',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If enabled, the product description will be written in a short description '
                                    . '(post_excerpt).',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'use_html_description' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Use for the main description "Description file for the site"',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If it is included, then the description of the product will be recorded not in '
                                    . 'the "Description", but in the "Description in HTML format" from the details of '
                                    . 'the product, if any, while the data from the "Description" will be recorded in '
                                    . 'a excerpt description of the product.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'more_check_image_changed' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Extra control over image changes',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'Turn this option on if you notice that changing the image in 1C does not lead '
                                    . 'to a change on the site. This can occur in a number of configurations in '
                                    . 'which the file name does not change when the image is changed.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'get_product_sku_from' => [
                                'type' => 'select',
                                'title' => esc_html__('Get product sku from:', 'itgalaxy-woocommerce-1c'),
                                'options' => [
                                    'sku' => esc_html__(
                                        'SKU',
                                        'itgalaxy-woocommerce-1c'
                                    ),
                                    'requisite_code' => esc_html__(
                                        'Requisite value "Code"',
                                        'itgalaxy-woocommerce-1c'
                                    ),
                                    'code' => esc_html__(
                                        'Code',
                                        'itgalaxy-woocommerce-1c'
                                    )
                                ],
                                'description' => esc_html__(
                                    'Indicate from which value the article number should be written.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                        ]
                    ],
                    [
                        'title' => esc_html__('Skipping / excluding data', 'itgalaxy-woocommerce-1c'),
                        'fields' => [
                            'skip_products_without_photo' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Skip products without photo',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If enabled, then products without photos will not be added to the site.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'skip_post_content_excerpt' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Skip product description',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If enabled, description and except will not be writed or modified.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'skip_post_title' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Do not update product title',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If enabled, the product title will be writed when the product is created and '
                                        . 'will no longer be changed according to the upload data.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'skip_post_images' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Do not update product images',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If enabled, the product images will be writed when the product is created '
                                    . '(if there is) and will no longer be changed according to the upload data.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'skip_post_attributes' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Do not update product attributes',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If enabled, the product attributes will be writed when the product is created and '
                                    . 'will no longer be changed according to the upload data.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'skip_categories' => [
                                'type' => 'checkbox',
                                'title' => esc_html__(
                                    'Do not process groups',
                                    'itgalaxy-woocommerce-1c'
                                ),
                                'description' => esc_html__(
                                    'If enabled, then categories on the site will not be created / updated based on '
                                    . 'data about groups from 1C, and the category will not be assigned / changed to '
                                    . 'products.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                        ]
                    ],
                    [
                        'title' => esc_html__('Exchange orders with 1C', 'itgalaxy-woocommerce-1c'),
                        'fields' => [
                            'send_orders' => [
                                'type' => 'checkbox',
                                'title' => esc_html__('Upload orders', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If enabled, when exchanging with 1C, the site gives all changed and new orders '
                                        . 'since the last synchronization.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'send_orders_response_encoding' => [
                                'type' => 'select',
                                'title' => esc_html__('Response encoding:', 'itgalaxy-woocommerce-1c'),
                                'options' => [
                                    'utf-8' => esc_html__(
                                        'UTF-8',
                                        'itgalaxy-woocommerce-1c'
                                    ),
                                    'cp1251' => esc_html__(
                                        'CP1251 (windows-1251)',
                                        'itgalaxy-woocommerce-1c'
                                    )
                                ],
                                'description' => esc_html__(
                                    'If you have a problem with receiving orders and in 1C you see an error like '
                                        . '"Failed to read XML", try changing the encoding.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'send_orders_last_success_export' => [
                                'title' => esc_html__('Date / time of last request:', 'itgalaxy-woocommerce-1c'),
                                'type' => 'datetime-local',
                                'description' => esc_html__(
                                    'At the next request for loading orders, which will come from 1C, the plugin will '
                                        . 'unload new / changed orders starting from this date / time.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                        ]
                    ],
                    [
                        'title' => esc_html__('For debugging', 'itgalaxy-woocommerce-1c'),
                        'fields' => [
                            'show_1c_code_metabox' => [
                                'type' => 'checkbox',
                                'title' => esc_html__('Show code 1C', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If enabled, the metabox with the code will appear in the admin panel, as well as '
                                        . 'when viewing the list - the code will be displayed under the product name.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'not_delete_exchange_files' => [
                                'type' => 'checkbox',
                                'title' => esc_html__('Do not delete files received from 1C', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'It may be useful during debugging to analyze the contents of the received XML '
                                        . 'files.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'force_update_product' => [
                                'type' => 'checkbox',
                                'title' => esc_html__('Force update products', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'Ignore the control of product changes by hash of the contents and update anyway. '
                                        . 'It may be useful, for example, if you made changes in the administrative panel, '
                                        . 'and not in 1C, and now you want to overwrite the data.',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ],
                            'remove_missing_variation' => [
                                'type' => 'checkbox',
                                'title' => esc_html__('Clean up missing product variations', 'itgalaxy-woocommerce-1c'),
                                'description' => esc_html__(
                                    'If enabled, then all variations of goods that are not in the unloading will be '
                                    . 'deleted. Be careful and use it only with a full exchange!',
                                    'itgalaxy-woocommerce-1c'
                                )
                            ]
                        ]
                    ]
                ];

                foreach ($fields as $section) {
                    self::showSection($section);
                }
                ?>

                <div class="postbox wc1c-padding">
                    <h3 class="hndle">
                        <?php esc_html_e('Exchange logging', 'itgalaxy-woocommerce-1c'); ?>
                    </h3>
                    <div class="inside">
                        <div>
                            <p class="description">
                                <?php esc_html_e('Logs of exchange with 1C are recorded in this directory. If it is not available for writing and reading, then logging will not work.', 'itgalaxy-woocommerce-1c'); ?>
                            </p>
                            <?php
                            $dirName = ITGALAXY_WC_1C_PLUGIN_DIR . 'files/site' . get_current_blog_id() . '/logs';
                            $message = Helper::existOrCreateDir($dirName);

                            if (!$message['status']) { ?>
                                <span style="<?php echo esc_attr($message['color']); ?>">
                                    <?php echo esc_html($message['text']); ?>
                                </span>
                                <?php
                            } else { ?>
                                <span style="<?php echo esc_attr($message['color']); ?>">
                                    <?php echo esc_html($message['text']); ?>
                                </span>
                                <div>
                                    <?php esc_html_e('Store for:', 'itgalaxy-woocommerce-1c'); ?>
                                    <input type="text"
                                        name="old_day_synchronization_logs"
                                        value="<?php echo esc_attr($oldDaySynchronizationLogs); ?>">
                                    <?php esc_html_e('days', 'itgalaxy-woocommerce-1c'); ?>
                                    <p class="description">
                                        <?php esc_html_e('Logs older will be deleted when exchanging.', 'itgalaxy-woocommerce-1c'); ?>
                                    </p>
                                </div>
                                <hr>
                                <?php
                                self::showCheckbox(
                                    [
                                        'title' => esc_html__('Enable logging', 'itgalaxy-woocommerce-1c'),
                                        'description' => esc_html__(
                                            'If enabled, when exchanging from 1C, logs of the exchange protocol are '
                                                . 'recorded.',
                                            'itgalaxy-woocommerce-1c'
                                        )
                                    ],
                                    'enable_logs_protocol',
                                    $settings
                                );

                                self::showCheckbox(
                                    [
                                        'title' => esc_html__('Enable change logging', 'itgalaxy-woocommerce-1c'),
                                        'description' => esc_html__(
                                            'If enabled, then when exchanging from 1C, logs of changes of objects are '
                                                . 'recorded.',
                                            'itgalaxy-woocommerce-1c'
                                        )
                                    ],
                                    'enable_logs_changes',
                                    $settings
                                );
                                ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <hr>
                <p class="submit">
                    <input type="submit"
                        class="button button-primary"
                        value="<?php esc_attr_e('Save settings', 'itgalaxy-woocommerce-1c'); ?>"
                        name="Submit">
                </p>
            </form>
            <hr>
            <?php
            if (isset($_POST['purchase-code'])) {
                $code = trim(wp_unslash($_POST['purchase-code']));

                $response = \wp_remote_post(
                    'https://wordpress-plugins.xyz/envato/license.php',
                    [
                        'body' => [
                            'purchaseCode' => $code,
                            'itemID' => '24768513',
                            'action' => isset($_POST['verify']) ? 'activate' : 'deactivate',
                            'domain' => site_url()
                        ],
                        'timeout' => 20
                    ]
                );

                if (is_wp_error($response)) {
                    $messageContent = '(Code - '
                        . $response->get_error_code()
                        . ') '
                        . $response->get_error_message();

                    $message = 'failedCheck';
                } else {
                    $response = json_decode(wp_remote_retrieve_body($response));

                    if ($response->status == 'successCheck') {
                        if (isset($_POST['verify'])) {
                            update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, $code);
                        } else {
                            update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');
                        }
                    } elseif (!isset($_POST['verify']) && $response->status == 'alreadyInactive') {
                        update_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');
                    }

                    $messageContent = $response->message;
                    $message = $response->status;
                }

                if ($message == 'successCheck') {
                    echo sprintf(
                        '<div class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                        esc_html($messageContent)
                    );
                } elseif ($messageContent) {
                    echo sprintf(
                        '<div class="error notice notice-error is-dismissible"><p>%s</p></div>',
                        esc_html($messageContent)
                    );
                }
            }

            $code = get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY);
            ?>
            <h1>
                <?php esc_html_e('License verification', 'itgalaxy-woocommerce-1c'); ?>
                <?php if ($code) { ?>
                    - <small style="color: green;">
                        <?php esc_html_e('verified', 'itgalaxy-woocommerce-1c'); ?>
                    </small>
                <?php } else { ?>
                    - <small style="color: red;">
                        <?php esc_html_e('please verify your purchase code', 'itgalaxy-woocommerce-1c'); ?>
                    </small>
                <?php } ?>
            </h1>
            <form method="post" action="#">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="purchase-code">
                                <?php esc_html_e('Purchase code', 'itgalaxy-woocommerce-1c'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                aria-required="true"
                                required
                                value="<?php
                                echo !empty($code)
                                    ? esc_attr($code)
                                    : '';
                                ?>"
                                id="purchase-code"
                                name="purchase-code"
                                class="large-text">
                            <small>
                                <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"
                                    target="_blank">
                                    <?php esc_html_e('Where Is My Purchase Code?', 'itgalaxy-woocommerce-1c'); ?>
                                </a>
                            </small>
                        </td>
                    </tr>
                </table>
                <p>
                    <input type="submit"
                        class="button button-primary"
                        value="<?php esc_attr_e('Verify', 'itgalaxy-woocommerce-1c'); ?>"
                        name="verify">
                    <?php if ($code) { ?>
                        <input type="submit"
                            class="button button-primary"
                            value="<?php esc_attr_e('Unverify', 'itgalaxy-woocommerce-1c'); ?>"
                            name="unverify">
                    <?php } ?>
                </p>
            </form>
        </div>
        <?php
    }

    public static function showSection($section)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        ?>
        <div class="postbox wc1c-padding">
            <h3 class="hndle">
                <?php echo esc_html($section['title']); ?>
            </h3>
            <div class="inside">
                <?php
                foreach ($section['fields'] as $name => $field) {
                    switch ($field['type']) {
                        case 'checkbox':
                            self::showCheckbox($field, $name, $settings);
                            break;
                        case 'select':
                            self::showSelect($field, $name, $settings);
                            break;
                        case 'text':
                        case 'datetime-local':
                            self::showInput($field, $name, $settings);
                            break;
                        default:
                            // Nothing
                            break;
                    }

                    echo end($section['fields']) !== $field ? '<hr>' : '';
                }
                ?>
            </div>
        </div>
        <?php
    }

    public static function showInput($field, $name, $settings)
    {
        $default = isset($field['default']) ? $field['default'] : '';
        ?>
        <div>
            <label for="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '_' . $name); ?>">
                <?php echo esc_html($field['title']); ?>
            </label>
            <input type="<?php echo isset($field['type']) ? esc_attr($field['type']) : 'text'; ?>"
                id="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '_' . $name); ?>"
                name="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '[' . $name . ']'); ?>"
                value="<?php echo isset($settings[$name]) ? esc_attr($settings[$name]) : $default; ?>">
            <?php if (!empty($field['description'])) { ?>
                <p class="description">
                    <?php echo esc_html($field['description']); ?>
                </p>
            <?php } ?>
        </div>
        <?php
    }

    public static function showCheckbox($field, $name, $settings)
    {
        ?>
        <div>
            <label>
                <input type="checkbox"
                    id="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '_' . $name); ?>"
                    name="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '[' . $name . ']'); ?>"
                    <?php echo !empty($settings[$name]) ? 'checked' : ''; ?>
                    value="1">
                <?php echo esc_html($field['title']); ?>
            </label>
            <?php if (!empty($field['description'])) { ?>
                <p class="description">
                    <?php echo esc_html($field['description']); ?>
                </p>
            <?php } ?>
        </div>
        <?php
    }

    public static function showSelect($field, $name, $settings)
    {
        ?>
        <div>
            <label for="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '_' . $name); ?>">
                <?php echo esc_html($field['title']); ?>
            </label>
            <select class="wc1c-settings-select"
                id="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '_' . $name); ?>"
                name="<?php echo esc_attr(Bootstrap::OPTIONS_KEY . '[' . $name . ']'); ?>">
                <?php
                foreach ($field['options'] as $optionValue => $optionLabel) {
                    echo '<option value="'
                        . esc_attr($optionValue)
                        . '"'
                        . (isset($settings[$name]) && $settings[$name] == $optionValue ? ' selected' : '')
                        . '>'
                        . esc_html($optionLabel)
                        . '</option>';
                }
                ?>
            </select>
            <?php if (!empty($field['description'])) { ?>
                <p class="description">
                    <?php echo esc_html($field['description']); ?>
                </p>
            <?php } ?>
        </div>
        <?php
    }

    private function tempCatalogInfo()
    {
        ?>
        <div class="postbox wc1c-padding">
            <h3 class="hndle">
                <?php esc_html_e('Temporary directory for exchange with 1C', 'itgalaxy-woocommerce-1c'); ?>
            </h3>
            <div class="inside">
                <div>
                    <?php
                    $dirName = ITGALAXY_WC_1C_PLUGIN_DIR . 'files/site' . get_current_blog_id() . '/temp';
                    $message = Helper::existOrCreateDir($dirName);
                    ?>
                    <p>
                        <span style="<?php echo esc_attr($message['color']); ?>">
                            <?php echo esc_html($message['text']); ?>
                        </span>
                    </p>
                    <p class="description">
                        <?php esc_html_e('Files received from 1C during the exchange are loaded into this directory if it is not available for write and read, sharing will be impossible.', 'itgalaxy-woocommerce-1c'); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    private function auth1cInfo()
    {
        ?>
        <div class="postbox wc1c-padding">
            <h3 class="hndle">
                <?php esc_html_e('Settings for authorization 1C', 'itgalaxy-woocommerce-1c'); ?>
            </h3>
            <div class="inside">
                <p class="description">
                    <?php esc_html_e('Use these details when setting up an exchange node in 1C.', 'itgalaxy-woocommerce-1c'); ?>
                </p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label>
                                <?php esc_html_e('Sync Script Address:', 'itgalaxy-woocommerce-1c'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                class="large-text"
                                readonly
                                value="<?php echo esc_url(get_bloginfo('url')); ?>/import-1c.php">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php esc_html_e('User:', 'itgalaxy-woocommerce-1c'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                class="regular-text"
                                name="synchronization_user"
                                value="<?php echo esc_attr(get_option('synchronization_user')); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php esc_html_e('Password:', 'itgalaxy-woocommerce-1c'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="password"
                                class="regular-text"
                                name="synchronization_pass"
                                value="<?php echo esc_attr(get_option('synchronization_pass')); ?>">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }

    private function extensionNotices()
    {
        // check exists php-xml extension
        if (!class_exists('\\XMLReader')) {
            echo sprintf(
                '<div class="error notice notice-error"><p><strong>%1$s</strong>: %2$s</p></div>',
                esc_html__('1C Data Exchange', 'itgalaxy-woocommerce-1c'),
                esc_html__(
                    'There is no extension "php-xml", without it, the exchange will not work. '
                    . 'Please install / activate the extension.',
                    'itgalaxy-woocommerce-1c'
                )
            );
        }

        // check exists php-zip extension
        if (!function_exists('zip_open')) {
            echo sprintf(
                '<div class="error notice notice-error"><p><strong>%1$s</strong>: %2$s</p></div>',
                esc_html__('1C Data Exchange', 'itgalaxy-woocommerce-1c'),
                esc_html__(
                    'There is no extension "php-zip", so the exchange in the archive will not work. '
                    . 'Please install / activate the extension.',
                    'itgalaxy-woocommerce-1c'
                )
            );
        }
    }
}
