<?php

namespace DL\DownloadManager;

use League\Plates\Engine;

class Post
{

    /**
     * Reemplazamos la plantilla
     * @param mixed $template
     * @author Daniel Lúcia
     */
    public function loadCustomTemplate($template)
    {
        global $post;

        if ($post->post_type == Constant::KEY) {
            $custom_template = plugin_dir_path(DL_DOWNLOAD_MANAGER_FILE) . 'src/Views/single-download.php';

            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        return $template;
    }

    /**
     * Renderizamos los datos de la descarga
     * @param mixed $post
     * @param mixed $versions
     * @return void
     * @author Daniel Lúcia
     */
    public function renderSingleTemplate($post, $versions)
    {
        $template_folder = plugin_dir_path(DL_DOWNLOAD_MANAGER_FILE) . 'src/Views/';
        $template = new Engine($template_folder);

        echo $template->render('public-table', [
            'post' => $post,
            'versions' => $versions,
            'download' => new Download(),
            'github_url' => get_post_meta($post->ID, '_dl_github', true)
        ]);
    }
}
