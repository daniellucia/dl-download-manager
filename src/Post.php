<?php

namespace DL\DownloadManager;

use League\Plates\Engine;

class Post
{
    public function showAfterContent($content)
    {
        if (is_singular(Constant::KEY)) {
            $post = get_post();
            $versions = new Versions($post);
            $content .= $this->renderDownloadSection($post, $versions->get());
        }

        return $content;
    }

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

    private function renderDownloadSection($post, $versions)
    {

        $template_folder = plugin_dir_path(DL_DOWNLOAD_MANAGER_FILE) . 'src/Views/';
        $template = new Engine($template_folder);
        $response = $template->render('public-table', [
            'versions' => $versions,
            'download' => new Download(),
            'post' => $post
        ]);

        return $response;
    }

    public function renderSingleTemplate($post, $versions)
    {
        $template_folder = plugin_dir_path(DL_DOWNLOAD_MANAGER_FILE) . 'src/Views/';
        $template = new Engine($template_folder);

        echo $template->render('single-download-content', [
            'post' => $post,
            'versions' => $versions,
            'download' => new Download(),
            'github_url' => get_post_meta($post->ID, '_dl_github', true)
        ]);
    }
}
