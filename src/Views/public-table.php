
<table class="dl-download-table">
    <thead>
        <tr>
            <?php do_action('dl_download_manager_before_version_header', $post); ?>
            <th><?php echo __('Version', 'dl-download-manager'); ?></th>
            <th><?php echo __('Download', 'dl-download-manager'); ?></th>
            <th width="50"><?php echo __('Downloads', 'dl-download-manager'); ?></th>
            <?php do_action('dl_download_manager_after_version_header', $post); ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($versions as $version): ?>
            <tr>

                <?php do_action('dl_download_manager_before_version_row', $version, $post); ?>

                <td><?php echo esc_html($version['version']); ?></td>
                <td>
                    <?php if (!empty($version['attachment_id']) && !empty($version['token'])): ?>
                        <a href="<?php echo esc_url($download->generate_download_url($post->ID, $version['token'])); ?>" target="_blank">
                            <?php echo __('Descargar', 'dl-download-manager'); ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td><?php echo intval($version['downloads'] ?? 0); ?></td>

                <?php do_action('dl_download_manager_after_version_row', $version, $post); ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>