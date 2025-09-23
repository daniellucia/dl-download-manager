<?php

namespace DL\DownloadManager;

class Download
{

    /**
     * Manejador para descargar el fichero
     * @return void
     * @author Daniel Lucia
     */
    public function handle()
    {
        $action = get_query_var('action');

        if ($action == Constant::KEY) {

            if (! is_user_logged_in()) {
                wp_die('You must be logged in to download this file.');
            }

            $post_id = get_query_var('download_id');

            if ($post_id <= 0) {
                wp_die('Invalid download ID.');
            }

            $token = get_query_var('token');

            if (! $token) {
                wp_die('Invalid token.');
            }

            $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : false;
            if (! wp_verify_nonce($nonce, 'dl_download_' . $post_id . '_' . $token)) {
                wp_die('Invalid nonce.');
            }

            $versions = new Versions($post_id);
            $file = $versions->getFileByToken($token);
            $post = get_post($post_id);

            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $filename = sanitize_title($post->post_name) . '.' . $extension;

            if ($file && file_exists($file)) {
                $versions->incrementDownloads($token);

                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                readfile($file);
                exit;
            } else {
                wp_die('Archivo no encontrado.');
            }
        }
    }

    /**
     * Genera una URL segura de descarga para un fichero de un post
     * @param int $post_id
     * @param string $file
     * @return string
     * @author Daniel Lucia
     */
    public function generate_download_url($post_id, $token)
    {
        $nonce = wp_create_nonce('dl_download_' . $post_id . '_' . $token);
        $action = Constant::KEY;

        $url =  add_query_arg([
            'nonce' => $nonce
        ], home_url("/{$action}/{$post_id}/{$token}"));

        return $url;
    }

    /**
     * Incrementa el contador de descargas de un post
     * @param int $post_id
     * @return void
     * @author Daniel Lucia
     */
    public function increment(int $post_id): void
    {
        $count = (int) get_post_meta($post_id, '_dl_download_count', true);
        update_post_meta($post_id, '_dl_download_count', $count + 1);
    }

    /**
     * Añade las reglas de reescritura para las URLs de descarga
     * @return void
     * @author Daniel Lucia
     */
    public function add_rewrite_rules()
    {
        add_rewrite_rule(
            '^' . Constant::KEY . '/([0-9]+)/([^/]+)/?$',
            'index.php?download_id=$matches[1]&token=$matches[2]&action=' . Constant::KEY,
            'top'
        );
        add_rewrite_tag('%download_id%', '([0-9]+)');
        add_rewrite_tag('%token%', '([^&]+)');
        add_rewrite_tag('%action%', '([^&]+)');
    }
}
