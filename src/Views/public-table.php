<div class="dl-download-section">

    <h3><?php echo __('Available Downloads', 'dl-download-manager'); ?></h3>

    <?php if (!empty($versions)) : ?>

        <div class="dl-download-info">
            <p><?php echo __('Choose your preferred version to download:', 'dl-download-manager'); ?></p>
        </div>

        <table class="dl-download-table">
            <thead>
                <tr>
                    <?php do_action('dl_download_manager_before_version_header', $post); ?>
                    <th><?php echo __('Version', 'dl-download-manager'); ?></th>
                    <th><?php echo __('Download', 'dl-download-manager'); ?></th>
                    <th><?php echo __('Downloads', 'dl-download-manager'); ?></th>
                    <th><?php echo __('Date', 'dl-download-manager'); ?></th>
                    <?php do_action('dl_download_manager_after_version_header', $post); ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($versions as $version): ?>
                    <tr>
                        <?php do_action('dl_download_manager_before_version_row', $version, $post); ?>

                        <td>
                            <strong><?php echo esc_html($version['version']); ?></strong>
                        </td>

                        <td>
                            <?php if (!empty($version['attachment_id']) && !empty($version['token'])): ?>
                                <a href="<?php echo esc_url($download->generate_download_url($post->ID, $version['token'])); ?>"
                                    class="dl-download-btn" target="_blank">
                                    <span class="dashicons dashicons-download"></span>
                                    <?php echo __('Download', 'dl-download-manager'); ?>
                                </a>
                            <?php else: ?>
                                <span class="dl-unavailable"><?php echo __('Not available', 'dl-download-manager'); ?></span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="dl-download-count"><?php echo intval($version['downloads'] ?? 0); ?></span>
                        </td>

                        <td>
                            <time datetime="<?php echo esc_attr($version['date_created']); ?>">
                                <?php echo date_i18n(get_option('date_format'), strtotime($version['date_created'])); ?>
                            </time>
                        </td>

                        <?php do_action('dl_download_manager_after_version_row', $version, $post); ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else : ?>

        <div class="dl-no-downloads">
            <p><?php echo __('No downloads are currently available for this item.', 'dl-download-manager'); ?></p>
        </div>

    <?php endif; ?>

    <?php if (!empty($github_url)) : ?>
        <div class="dl-github-link">
            <h4><?php echo __('Source Code', 'dl-download-manager'); ?></h4>
            <p>
                <a href="<?php echo esc_url($github_url); ?>" target="_blank" rel="noopener">
                    <span class="dashicons dashicons-admin-links"></span>
                    <?php echo __('View on GitHub', 'dl-download-manager'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>

</div>