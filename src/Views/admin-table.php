
<table class="widefat" style="margin-bottom:20px;" id="dl_versions">
    <thead>
        <tr>
            <th><?php echo __('Version', 'dl-download-manager'); ?></th>
            <th><?php echo __('Download', 'dl-download-manager'); ?></th>
            <th width="50"><?php echo __('Downloads', 'dl-download-manager'); ?></th>
            <th width="90"><?php echo __('Remove', 'dl-download-manager'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $downloads = 0; ?>
        <?php foreach ($versions as $version): ?>
            <tr>
                <td><?php echo esc_html($version['version']); ?></td>
                <td>
                    <?php if (!empty($version['attachment_id']) && !empty($version['token'])): ?>
                        <a href="<?php echo esc_url($download->generate_download_url($post->ID, $version['token'])); ?>" target="_blank">
                            <?php echo __('Descargar', 'dl-download-manager'); ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    echo intval($version['downloads'] ?? 0); 
                    $downloads += intval($version['downloads'] ?? 0);
                    ?>
                </td>
                <td>
                    <?php
                    $remove_url = add_query_arg([
                        'action' => 'dl_remove_version',
                        'post_id' => $post->ID,
                        'token' => $version['token'],
                        'nonce' => wp_create_nonce('dl_remove_version_' . $post->ID . '_' . $version['token'])
                    ], admin_url('admin-post.php'));
                    ?>
                    <a href="<?php echo esc_url($remove_url); ?>"
                       onclick="return confirm('<?php echo esc_js(__('Are you sure you want to remove this version?', 'dl-download-manager')); ?>');">
                        <?php echo __('Remove', 'dl-download-manager'); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        <tfooter>
            <tr>
                <td></td>
                <td></td>
                <td><strong><?php echo $downloads; ?></strong></td>
                <td></td>
            </tr>
        </tfooter>
    </tbody>
</table>