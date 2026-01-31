<?php
namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess;

use Itgalaxy\Wc\Exchange1c\Includes\Helper;
use Itgalaxy\Wc\Exchange1c\Includes\Bootstrap;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;

class RootProcessStarter
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
        // check session is start
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->preparePaths();
        $this->checkExistsXmlExtension();
        $this->checkEnableExchange();
        $this->checkAuth();
        $this->prepareExchangeFileName();

        switch ($_GET['type']) {
            case 'catalog':
                // catalog exchange
                CatalogProcessStarter::getInstance();
                break;
            case 'sale':
                // order exchange
                SaleProcessStarter::getInstance();
                break;
            default:
                // Nothing
                break;
        }

        // stop execution anyway
        exit();
    }

    private function preparePaths()
    {
        $_SESSION['synchronization1cPathTemp'] = ITGALAXY_WC_1C_PLUGIN_DIR
            . 'files/site' . get_current_blog_id() . '/temp';
        $_SESSION['synchronization1cPathLogs'] = ITGALAXY_WC_1C_PLUGIN_DIR
            . 'files/site' . get_current_blog_id() . '/logs';

        Helper::existOrCreateDir($_SESSION['synchronization1cPathTemp']);
        Helper::existOrCreateDir($_SESSION['synchronization1cPathLogs']);
    }

    private function checkExistsXmlExtension()
    {
        if (class_exists('\\XMLReader')) {
            return;
        }

        echo "failure\n"
            . 'Please install/enable `php-xml` extension for PHP';
        // 1c response does not require escape

        Logger::logProtocol(
            'failure '
            . 'Please install/enable `php-xml` extension for PHP'
        );

        exit();
    }

    private function checkEnableExchange()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        // exchange enabled
        if (!empty($settings['enable_exchange'])) {
            return;
        }

        echo "failure\n "
            . Helper::convertMessage(
                esc_html__('Error! Synchronization is prohibited!', 'itgalaxy-woocommerce-1c')
            );
        // 1c response does not require escape

        Logger::logProtocol(
            'failure '
            . esc_html__('Error! Synchronization is prohibited!', 'itgalaxy-woocommerce-1c')
        );

        exit();
    }

    private function checkAuth()
    {
        if (is_super_admin()) {
            return;
        }

        if (
            empty($_SERVER['PHP_AUTH_USER']) ||
            empty($_SERVER['PHP_AUTH_PW'])
        ) {
            $this->fixCgiAuth();
        }

        if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
            echo "failure\n " . Helper::convertMessage(
                    esc_html__('Error! Empty login or password! Maybe not configured php-fpm.', 'itgalaxy-woocommerce-1c')
                );
            // 1c response does not require escape

            Logger::logProtocol('failure ' . esc_html__('Error! Empty login or password! Maybe not configured php-fpm.', 'itgalaxy-woocommerce-1c'));

            exit();
        }

        // wrong login or password
        if (
            wp_unslash($_SERVER['PHP_AUTH_USER']) !== strip_tags(get_option('synchronization_user')) ||
            wp_unslash($_SERVER['PHP_AUTH_PW']) !== strip_tags(get_option('synchronization_pass'))
        ) {
            echo "failure\n " . Helper::convertMessage(
                    esc_html__('Error! Wrong login or password!', 'itgalaxy-woocommerce-1c')
                );
            // 1c response does not require escape

            Logger::logProtocol('failure ' . esc_html__('Error! Wrong login or password!', 'itgalaxy-woocommerce-1c'));

            exit();
        }
    }

    // https://www.php.net/manual/ru/features.http-auth.php#106285
    // method fills in empty user and password variables
    private function fixCgiAuth()
    {
        $environmentVariables = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION'
        ];

        foreach ($environmentVariables as $environmentVariable) {
            if (empty($_SERVER[$environmentVariable])) {
                continue;
            }

            if (preg_match('/Basic\s+(.*)$/i', $_SERVER[$environmentVariable], $matches) === 0) {
                continue;
            }

            list($name, $password) = explode(':', base64_decode($matches[1]));
            $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
            $_SERVER['PHP_AUTH_PW'] = strip_tags($password);
        }
    }

    private function prepareExchangeFileName()
    {
        // create a file name and create a directory (if not) to load it
        if (!empty($_GET['filename'])) {
            $filename = trim(str_replace('\\', '/', trim($_GET['filename'])), "/");
            $filename = $_SESSION['synchronization1cPathTemp'] . '/' . $filename;

            if (!file_exists(dirname($filename))) {
                mkdir(dirname($filename), 0775, true);
            }

            $_SESSION['1cExchangefilename'] = $filename;
        } else {
            $_SESSION['1cExchangefilename'] = '';
        }
    }
}
