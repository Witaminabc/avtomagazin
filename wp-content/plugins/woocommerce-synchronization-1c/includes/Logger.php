<?php
namespace Itgalaxy\Wc\Exchange1c\Includes;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

class Logger
{
    public static $format = '[%datetime% | %ip% | %user% | %method% | %query%] %channel%.%level_name%: '
        . "%message% %context% %extra%\n";

    public static $log;

    public static function logProtocol($message, $data = [])
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (
            !empty($settings['enable_logs_protocol']) &&
            is_writable($_SESSION['synchronization1cPathLogs'])
        ) {
            if (empty($_SESSION['logSynchronizeProcessFile'])) {
                // prepare and set log file path
                self::setLogFilePathToSession(
                    self::generateLogFilePath()
                );
            }

            self::log($_SESSION['logSynchronizeProcessFile'], $message, $data);
        }
    }

    public static function logChanges($action, $type, $id, $value = '')
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (empty($settings['enable_logs_changes'])) {
            return;
        }

        $logsPath = $_SESSION['synchronization1cPathLogs'] . '/';

        if (!is_writable($logsPath)) {
            return;
        }

        if (empty($_SESSION['logSynchronizeProcessFile'])) {
            // prepare and set log file path
            self::setLogFilePathToSession(
                self::generateLogFilePath()
            );
        }

        $message = '';

        switch ($type) {
            case 'not_exists_parent_product':
                $message = 'Error! Not exists parent product ' . $value;
                break;
            case 'term':
                $afterMessage = ' ( term_id - ' . $id . ')'
                    . ' - '
                    . get_term_meta($id, '_id_1c', true);

                switch ($action) {
                    case 'insert':
                        $message = 'Added Term ' . $afterMessage;
                        break;
                    case 'update':
                        $message = 'Updated Term ' . $afterMessage;
                        break;
                    case 'remove':
                        $message = 'Removed Term ' . $afterMessage;
                        break;
                    default:
                        break;
                }
                break;
            case 'product':
                $afterMessage = ' ( ID - ' . $id . ') '
                    . $value
                    . ' - '
                    . get_post_meta($id, '_id_1c', true);

                switch ($action) {
                    case 'set_variation_attributes':
                        $message = 'Set variation attributes ' . $afterMessage;
                        break;
                    case 'insert':
                        $message = 'Added Product ' . $afterMessage;
                        break;
                    case 'update':
                        $message = 'Updated Product ' . $afterMessage;
                        break;
                    case 'remove':
                        $message = 'Removed Product ' . $afterMessage;
                        break;
                    case 'current_exchange_variation_list':
                    case 'remove_variation':
                        $message = 'Removed Variation (' . $action . ') ' . $afterMessage;
                        break;
                    default:
                        break;
                }
                break;
            case 'product_image':
                $afterMessage = ' ( ID - ' . $id . ')'
                    . ' - '
                    . get_post_meta($id, '_id_1c', true);

                switch ($action) {
                    case 'insert':
                        $message = 'Added Mediafile for ' . $afterMessage;
                        break;
                    case 'remove':
                        $message = 'Removed Mediafile for ' . $afterMessage;
                        break;
                    default:
                        break;
                }
                break;
            case 'product_price':
                $afterMessage = ' ( ID - ' . $id . ') value - '
                    . $value
                    . ' - '
                    . get_post_meta($id, '_id_1c', true);

                switch ($action) {
                    case 'update':
                        $message = 'Updated Price set for ' . $afterMessage;
                        break;
                    default:
                        break;
                }
                break;
            case 'product_sale_price':
                $afterMessage = ' ( ID - ' . $id . ') value - '
                    . $value
                    . ' - '
                    . get_post_meta($id, '_id_1c', true);

                switch ($action) {
                    case 'update':
                        $message = 'Updated Sale price set for ' . $afterMessage;
                        break;
                    default:
                        break;
                }
                break;
            case 'product_stock':
                $afterMessage = ' ( ID - ' . $id . ') value - '
                    . $value
                    . ' - '
                    . get_post_meta($id, '_id_1c', true);

                switch ($action) {
                    case 'update':
                        $message = 'Updated Stock set for ' . $afterMessage;
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }

        self::log($_SESSION['logSynchronizeProcessFile'], $message);
    }

    public static function log($file, $message, $data = [], $type = 'info')
    {
        try {
            if (empty(self::$log)) {
                self::$log = new MonologLogger('wc1c');

                $handler = new StreamHandler($file, MonologLogger::INFO);
                $handler->setFormatter(new LineFormatter(self::$format));

                self::$log->pushHandler($handler);

                self::$log->pushProcessor(function ($entry) {
                    return self::addClientData($entry);
                });
            }

            self::$log->$type($message, (array) $data);
        } catch (\Exception $exception) {
            if (is_super_admin()) {
                wp_die(
                    sprintf(
                        esc_html__(
                            'Error code (%s): %s.',
                            'itgalaxy-woocommerce-1c'
                        ),
                        $exception->getCode(),
                        $exception->getMessage()
                    ),
                    esc_html__(
                        'An error occurred while writing the log file.',
                        'itgalaxy-woocommerce-1c'
                    ),
                    [
                        'back_link' => true
                    ]
                );
                // escape ok
            }
        }
    }

    public static function clearOldLogs()
    {
        $logsPath = $_SESSION['synchronization1cPathLogs'] . '/';
        $oldDaySynchronizationLogs = (int) get_option('old_day_synchronization_logs');

        if ($oldDaySynchronizationLogs <= 1) {
            $oldDaySynchronizationLogs = 30;
        }

        $expireTime = $oldDaySynchronizationLogs * 24 * 60 * 60; // time in seconds - default 30 days

        if (is_dir($logsPath)) {
            $dirHandler = opendir($logsPath);

            if ($dirHandler) {
                while (($file = readdir($dirHandler)) !== false) {
                    $timeSec = time();
                    $filePath = $logsPath . $file;
                    $timeFile = filemtime($filePath);

                    $time = $timeSec - $timeFile;

                    if (is_file($filePath) && $time > $expireTime) {
                        unlink($filePath);
                    }
                }

                closedir($dirHandler);
            }
        }
    }

    private static function generateLogFilePath()
    {
        $logsPath = $_SESSION['synchronization1cPathLogs'] . '/';

        return $logsPath . 'catalog_update_' . date_i18n('Y.m.d_H-i-s') . '.log1c';
    }

    private static function setLogFilePathToSession($logFile)
    {
        $_SESSION['logSynchronizeProcessFile'] = $logFile;
    }

    private static function addClientData($record)
    {
        $record['ip'] = $_SERVER['REMOTE_ADDR'];
        $record['method'] = $_SERVER['REQUEST_METHOD'];
        $record['query'] = $_SERVER['QUERY_STRING'];
        $record['user'] = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : 'non user';

        return $record;
    }
}
