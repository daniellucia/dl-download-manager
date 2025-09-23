<?php

namespace DL\DownloadManager;

use League\Plates\Engine;

class CPT
{
    /**
     * Registramos cpt
     * @return void
     * @author Daniel Lucia
     */
    public function register_post_type()
    {
        register_post_type(
            Constant::KEY,
            [
                'label' => __('Downloads', 'dl-download-manager'),
                'public' => true,
                'supports' => ['title', 'editor', 'thumbnail'],
                'has_archive' => true,
                'rewrite' => ['slug' => 'dldownloads'],
                'menu_icon' => 'dashicons-download',
            ]
        );
    }

    /**
     * Registramos taxonomía
     * @return void
     * @author Daniel Lucia
     */
    public function register_taxonomy()
    {
        register_taxonomy('dldownload_category', Constant::KEY, [
            'label' => __('Categories', 'dl-download-manager'),
            'hierarchical' => true,
            'rewrite' => ['slug' => 'dl-category-downloads'],
        ]);
    }

    /**
     * Añadimos metabox
     * @return void
     * @author Daniel Lucia
     */
    public function add_meta_boxes()
    {
        add_meta_box(
            'dl_download_meta',
            __('Download information', 'dl-download-manager'),
            [$this, 'render_meta_box'],
            Constant::KEY,
            'normal',
            'high'
        );
    }


    /**
     * Añadimos contenido al metabox
     * @param mixed $post
     * @return void 1
     * @author Daniel Lucia
     */
    public function render_meta_box($post)
    {
        $versions = (new Versions($post))->get();

        wp_nonce_field('dl_download_meta', 'dl_download_meta_nonce');

        echo '<h4>' . __('Version history', 'dl-download-manager') . '</h4>';
        if (!empty($versions)) {
            echo $this->showVersionTable($versions);
        } else {
            echo '<p>' . __('There are no previous versions.', 'dl-download-manager') . '</p>';
        }

        echo '<hr>';
        echo '<h4>' . __('New version', 'dl-download-manager') . '</h4>';
        echo '<p><label>' . __('Version:', 'dl-download-manager') . ' <input type="text" name="dl_new_version" value="" style="width:100%"></label></p>';
        echo '<p><label>' . __('File:', 'dl-download-manager') . '<input type="file" name="dl_new_file" style="width:100%"></label></p>';

        $github = get_post_meta($post->ID, '_dl_github', true);
        echo '<p><label>' . __('URL de GitHub:', 'dl-download-manager') . '<input type="url" name="dl_github" value="' . esc_attr($github) . '" style="width:100%"></label></p>';
    }

    /**
     * Eliminamos una versión
     * @return never
     * @author Daniel Lucia
     */
    public function removeVersion(): bool
    {

        if (isset($_GET['action']) && $_GET['action'] == 'dl_remove_version') {

            $post_id = (int) $_GET['post_id'];
            $token = sanitize_text_field($_GET['token']);
            $nonce = sanitize_text_field($_GET['nonce']);

            if (!wp_verify_nonce($nonce, 'dl_remove_version_' . $post_id . '_' . $token)) {
                wp_die('Invalid nonce.');
            }

            $versions = new Versions($post_id);
            $versions->deleteByToken($token);

            wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit#dl_versions'));
            return true;
        }

        return false;
    }

    /**
     * Mostramos la tabla de versiones
     * @param mixed $versions
     * @return string
     * @author Daniel Lucia
     */
    private function showVersionTable($versions)
    {
        global $post;

        $template_folder = plugin_dir_path(DL_DOWNLOAD_MANAGER_FILE) . 'src/Views/';
        $template = new Engine($template_folder);
        $response = $template->render('admin-table', [
            'versions' => $versions,
            'download' => new Download(),
            'post' => $post
        ]);

        return $response;
    }

    /**
     * Guardamos los datos del metabox
     * @param mixed $post_id
     * @return void
     * @author Daniel Lucia
     */
    public function save_meta_boxes($post_id)
    {
        if (! isset($_POST['dl_download_meta_nonce']) || ! wp_verify_nonce($_POST['dl_download_meta_nonce'], 'dl_download_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['dl_github'])) {
            update_post_meta($post_id, '_dl_github', esc_url_raw($_POST['dl_github']));
        }

        $versions = (new Versions($post_id))->get();

        if (!empty($_POST['dl_new_version']) && !empty($_FILES['dl_new_file']['name'])) {
            // Subimos el archivo ZIP como adjunto de WordPress
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // 'dl_new_file' es el nombre del input file
            $attachment_id = media_handle_upload('dl_new_file', $post_id);

            if (!is_wp_error($attachment_id)) {
                $file_url = wp_get_attachment_url($attachment_id);

                // Guardamos la versión con el ID adjunto y la URL pública
                $versions[] = (new Versions($post_id))->create(
                    $attachment_id,
                    $_POST['dl_new_version'],
                    $file_url
                );

                update_post_meta($post_id, '_dl_versions', $versions);
            }
        }
    }

    /**
     * Añadimos el enctype al formulario de subida de archivos
     * @return void
     * @author Daniel Lucia
     */
    public function add_enctype($post)
    {
        if ($post->post_type !== Constant::KEY) {
            return;
        }

        echo ' enctype="multipart/form-data"';
    }
}
