<?php

/**
 * Plugin Name:       Download Manager
 * Description:       Secure download manager with versioning
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Daniel Lucia
 * Author URI:        http://www.daniellucia.es/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        http://www.daniellucia.es/
 * Text Domain:       dl-download-manager
 * Domain Path:       /languages
 */

/*
Copyright (C) 2025  Daniel Lucia (https://daniellucia.es)

Este programa es software libre: puedes redistribuirlo y/o modificarlo
bajo los términos de la Licencia Pública General GNU publicada por
la Free Software Foundation, ya sea la versión 2 de la Licencia,
o (a tu elección) cualquier versión posterior.

Este programa se distribuye con la esperanza de que sea útil,
pero SIN NINGUNA GARANTÍA; ni siquiera la garantía implícita de
COMERCIABILIDAD o IDONEIDAD PARA UN PROPÓSITO PARTICULAR.
Consulta la Licencia Pública General GNU para más detalles.

Deberías haber recibido una copia de la Licencia Pública General GNU
junto con este programa. En caso contrario, consulta <https://www.gnu.org/licenses/gpl-2.0.html>.
*/

use DL\DownloadManager\Plugin;

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

define('DL_DOWNLOAD_MANAGER_VERSION', '0.0.1');
define('DL_DOWNLOAD_MANAGER_FILE', __FILE__);

add_action('plugins_loaded', function () {

    load_plugin_textdomain('dl-download-manager', false, dirname(plugin_basename(DL_DOWNLOAD_MANAGER_FILE)) . '/languages');

    $plugin = new Plugin();
});

/**
 * Limpiamos caché al activar o desactivar el plugin
 */
register_activation_hook(__FILE__, function () {
    wp_cache_flush();
});

register_deactivation_hook(__FILE__, function () {
    wp_cache_flush();
});
