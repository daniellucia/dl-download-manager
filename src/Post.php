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
}
