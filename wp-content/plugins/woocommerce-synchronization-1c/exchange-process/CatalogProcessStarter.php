<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess;

use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\Includes\Cron;
use Itgalaxy\Wc\Exchange1c\Includes\Helper;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class CatalogProcessStarter
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
        if ($_GET['mode'] === 'checkauth') {
            $this->checkAuthModeProcessing();
        } else {
            $baseName = basename($_SESSION['1cExchangefilename']);

            if ($_GET['mode'] === 'init') {
                ob_start();

                $this->initModeProcessing();
            } elseif ($_GET['mode'] === 'file') {
                $this->fileModeProcessing($baseName);
            } elseif ($_GET['mode'] === 'import') {
                $message = '';
                $strError = '';
                $strMessage = '';

                if (!isset($_SESSION['IMPORT_1C'])) {
                    $_SESSION['IMPORT_1C'] = [];
                }

                if (!isset($_SESSION['IMPORT_1C_STEP'])) {
                    $_SESSION['IMPORT_1C_STEP'] = 1;
                }

                if ((int) $_SESSION['IMPORT_1C_STEP'] === 1) {
                    if (
                        isset($_SESSION['IMPORT_1C']['zip_file']) &&
                        file_exists($_SESSION['IMPORT_1C']['zip_file'])
                    ) {
                        Helper::extractArchive($_SESSION['IMPORT_1C']['zip_file']);

                        $strMessage = Helper::convertMessage(esc_html__('Archive unpacked', 'itgalaxy-woocommerce-1c'));

                        Logger::logProtocol(esc_html__('Archive unpacked', 'itgalaxy-woocommerce-1c'));
                    }

                    $_SESSION['IMPORT_1C_STEP'] = 2;
                }

                if ((int) $_SESSION['IMPORT_1C_STEP'] === 2) {
                    try {
                        // check requested parse file exists
                        if (!file_exists($_SESSION['1cExchangefilename'])) {
                            echo "failure\n "
                                . esc_html('File not exists! - ' . basename($_SESSION['1cExchangefilename']));
                            // 1c response does not require escape

                            Logger::logProtocol(
                                esc_html('failure - File not exists! - ' . basename($_SESSION['1cExchangefilename']))
                            );

                            exit();
                        }

                        // get version scheme
                        $reader = new \XMLReader();
                        $reader->open($_SESSION['1cExchangefilename']);
                        $reader->read();
                        $version = (float) $reader->getAttribute('ВерсияСхемы');
                        $reader->close();
                        unset($reader);

                        // resolve parser base version
                        if ($version < 3) {
                            $Parser1cXml = new Parser1cXml();
                        } else {
                            $Parser1cXml = new Parser1cXml31();
                        }

                        $_SESSION['xmlVersion'] = $version;

                        // load required image working functions
                        require_once ABSPATH . 'wp-admin/includes/image.php';
                        require_once ABSPATH . 'wp-admin/includes/file.php';
                        require_once ABSPATH . 'wp-admin/includes/media.php';
                        require_once ABSPATH . 'wp-includes/pluggable.php';

                        if ($Parser1cXml->parce($_SESSION['1cExchangefilename'])) {
                            $_SESSION['IMPORT_1C_STEP'] = 3;
                            unset($_SESSION['IMPORT_1C']);

                            if (
                                strpos($baseName, 'offers') !== false ||
                                strpos($baseName, 'rests') !== false // scheme 3.1
                            ) {
                                unset($_SESSION['IMPORT_1C_PROCESS']);
                            }
                        } else {
                            // manual import auto progress
                            if (isset($_GET['manual-1c-import']) && is_super_admin()) {
                                header('refresh:1');
                            }

                            if (
                                strpos($baseName, 'import') !== false &&
                                !isset($_SESSION['IMPORT_1C']['heartbeat']['Товар'])
                            ) {
                                if (isset($_SESSION['IMPORT_1C']['numberOfCategories'])) {
                                    $count = $_SESSION['IMPORT_1C']['numberOfCategories'];

                                    $message = "progress "
                                        . esc_html__('Processing groups', 'itgalaxy-woocommerce-1c')
                                        . " {$baseName}... {$count}";

                                    $strMessage =
                                        Helper::convertMessage(
                                            esc_html__('Processing groups', 'itgalaxy-woocommerce-1c')
                                        )
                                        . ' '
                                        . $baseName
                                        . '...'
                                        . $count;
                                } else {
                                    $message = "progress "
                                        . 'Processing'
                                        . " {$baseName}...";

                                    $strMessage =
                                        'Processing '
                                        . $baseName
                                        . '...';
                                }
                            } else {
                                if (strpos($baseName, 'import') !== false) {
                                    $count = isset($_SESSION['IMPORT_1C']['heartbeat']['Товар'])
                                        ? $_SESSION['IMPORT_1C']['heartbeat']['Товар']
                                        : 0;
                                } else {
                                    $count = isset($_SESSION['IMPORT_1C']['heartbeat']['Предложение'])
                                        ? $_SESSION['IMPORT_1C']['heartbeat']['Предложение']
                                        : 0;
                                }

                                $message = "progress "
                                    . esc_html__('Reading file', 'itgalaxy-woocommerce-1c')
                                    . " {$baseName}... {$count}";

                                $strMessage = Helper::convertMessage(esc_html__('Reading file', 'itgalaxy-woocommerce-1c'))
                                    . ' '
                                    . $baseName
                                    . '...'
                                    . $count;
                            }
                        }
                    } catch (\Exception $e) {
                        $strError = $e->getMessage();
                    }
                } else {
                    $_SESSION['IMPORT_1C_STEP']++;
                }

                if ($strError) {
                    echo "failure\n{$strError}";
                    // 1c response does not require escape

                    $message = "failure {$strError}";
                } else {
                    if ($_SESSION['IMPORT_1C_STEP'] < 3) {
                        echo "progress\n" . $strMessage;
                        // 1c response does not require escape
                    } else {
                        $_SESSION['IMPORT_1C_STEP'] = 0;

                        echo "success\n "
                            . Helper::convertMessage(
                                esc_html__('Import file', 'itgalaxy-woocommerce-1c')
                                . ' '
                                . $baseName
                                . ' '
                                . esc_html__('completed!', 'itgalaxy-woocommerce-1c')
                            );
                        // 1c response does not require escape

                        $message = "success "
                            . esc_html__('Import file', 'itgalaxy-woocommerce-1c')
                            . ' '
                            . $baseName
                            . ' '
                            . esc_html__('completed!', 'itgalaxy-woocommerce-1c');
                    }
                }

                Logger::logProtocol($message);
            } elseif ($_GET['mode'] === 'complete') {
                $this->completeModeProcessing();
            } elseif ($_GET['mode'] === 'deactivate') {
                $this->deactivateModeProcessing();
            }
        }
    }

    private function checkAuthModeProcessing()
    {
        $sessionId = session_id();

        echo "success\n"
            . session_name()
            . "\n"
            . $sessionId
            . "\n";
        // 1c response does not require escape

        Logger::clearOldLogs();
        Logger::logProtocol("success {$sessionId}");
    }

    private function initModeProcessing()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!is_dir($_SESSION['synchronization1cPathTemp'])) {
            echo "failure\n " . Helper::convertMessage(esc_html__('Initialization Error!', 'itgalaxy-woocommerce-1c'));
            // 1c response does not require escape

            $message = 'failure ' . esc_html__('Initialization Error!', 'itgalaxy-woocommerce-1c');
        } else {
            if (
                empty($settings['not_delete_exchange_files']) &&
                is_writable($_SESSION['synchronization1cPathTemp'])
            ) {
                Helper::removeDir($_SESSION['synchronization1cPathTemp']);
                mkdir($_SESSION['synchronization1cPathTemp'], 0755, true);
            }

            $zip = Helper::getIsUseZip() ? 'yes' : 'no';

            echo "zip={$zip}\n" . 'file_limit=' . Helper::getFileSizeLimit();
            // 1c response does not require escape

            $message = 'zip=' . $zip . ', file_limit=' . Helper::getFileSizeLimit();
        }

        Logger::logProtocol($message);

        if (!isset($_SESSION['IMPORT_1C'])) {
            $_SESSION['IMPORT_1C'] = [];
        }

        if (!isset($_SESSION['IMPORT_1C_PROCESS'])) {
            $_SESSION['IMPORT_1C_PROCESS'] = [];
            $_SESSION['IMPORT_1C_PROCESS']['currentCategorys1c'] = [];
        }
    }

    private function fileModeProcessing($baseName)
    {
        if (function_exists('file_get_contents')) {
            $data = file_get_contents('php://input');
        } elseif (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $data = &$GLOBALS['HTTP_RAW_POST_DATA'];
        } else {
            $data = false;
        }

        if ($data !== false) {
            if (!is_writable(dirname($_SESSION['1cExchangefilename']))
                || (
                    file_exists($_SESSION['1cExchangefilename'])
                    && !is_writable($_SESSION['1cExchangefilename'])
                )
            ) {
                echo "failure\n"
                    . Helper::convertMessage(
                        esc_html__('The directory / file is not writable', 'itgalaxy-woocommerce-1c')
                        . ": {$baseName}"
                    );
                // 1c response does not require escape

                $message = "failure "
                    . esc_html__('The directory / file is not writable', 'itgalaxy-woocommerce-1c')
                    . ": {$baseName}";
            } else {
                $fp = fopen($_SESSION['1cExchangefilename'], 'ab');
                $result = fwrite($fp, $data);

                if ($result === mb_strlen($data, 'latin1')) {
                    echo "success\n";
                    // 1c response does not require escape

                    $message = 'success';

                    if (Helper::getIsUseZip()) {
                        $_SESSION['IMPORT_1C']['zip_file'] = $_SESSION['1cExchangefilename'];
                    }
                } else {
                    echo "failure\n "
                        . Helper::convertMessage(esc_html__('Error writing file!', 'itgalaxy-woocommerce-1c'));
                    // 1c response does not require escape

                    $message = 'failure ' . esc_html__('Error writing file!', 'itgalaxy-woocommerce-1c');
                }
            }
        } else {
            echo "failure\n "
                . Helper::convertMessage(esc_html__('Error reading http stream!', 'itgalaxy-woocommerce-1c'));
            // 1c response does not require escape

            $message = 'failure '
                . esc_html__('Error reading http stream!', 'itgalaxy-woocommerce-1c');
        }

        Logger::logProtocol($message);
    }

    private function completeModeProcessing()
    {
        echo "success\n " . Helper::convertMessage(esc_html__('Package complete!', 'itgalaxy-woocommerce-1c'));
        $message = 'success ' . esc_html__('Package complete!', 'itgalaxy-woocommerce-1c');

        if (!get_option('not_clear_1c_complete')) {
            update_option('all1cProducts', []);
            update_option('currentAll1cGroup', []);

            $cron = Cron::getInstance();
            $cron->createCronTermRecount();

            // clear sitemap cache
            if (class_exists('\\WPSEO_Sitemaps_Cache')) {
                remove_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');
                \WPSEO_Sitemaps_Cache::clear();
            }
        } else {
            update_option('not_clear_1c_complete', '');
        }

        Logger::logProtocol($message);
    }

    private function deactivateModeProcessing()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!empty($settings['remove_missing_products'])) {
            update_option('not_clear_1c_complete', 1);

            $cron = Cron::getInstance();
            $cron->createCronDisableItems();
        }

        echo "success\n " . Helper::convertMessage(esc_html__('Task deactivate registered!', 'itgalaxy-woocommerce-1c'));
        $message = 'success ' . esc_html__('Task deactivate registered!', 'itgalaxy-woocommerce-1c');

        Logger::logProtocol($message);
    }
}
