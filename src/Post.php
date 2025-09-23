<?php

namespace DL\DownloadManager;

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
        $response = '';
        $response .= '<table class="widefat" style="margin-bottom:20px;" id="dl_versions">';
        $response .= '<thead>
                    <tr>
                        <th>' . __('Version', 'dl-download-manager') . '</th>
                        <th width="50">' . __('Download', 'dl-download-manager') . '</th>
                    </tr>
                  </thead>';
        $response .= '<tbody>';
        foreach ($versions as $version) {
            $response .= '<tr>';
            $response .= '<td>' . esc_html($version['version']) . '</td>';

            $response .= '<td>';
            if (!empty($version['attachment_id']) && !empty($version['token'])) {
                $response .= '<a href="' . (new Download())->generate_download_url($post->ID, $version['token']) . '" target="_blank">' . __('Descargar', 'dl-download-manager') . '</a>';
            }
            $response .= '</td>';
            $response .= '</tr>';
        }
        $response .= '</tbody>';
        $response .= '</table>';

        return $response;
    }
}
