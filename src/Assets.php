<?php 

namespace DL\DownloadManager;

class Assets
{
    public function enqueue() {
        if (!is_admin()) {
            wp_enqueue_style(
                'dl-download-manager-public',
                plugins_url('assets/css/public.css', DL_DOWNLOAD_MANAGER_FILE),
                [],
                null
            );
        }
    }
}
