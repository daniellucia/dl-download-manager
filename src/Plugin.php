<?php

namespace DL\DownloadManager;

class Plugin
{

    public function __construct()
    {
        $cpt = new CPT();
        add_action('init', [$cpt, 'register_post_type']);
        add_action('init', [$cpt, 'register_taxonomy']);
        add_action('init', [$cpt, 'removeVersion']);
        add_action('add_meta_boxes', [$cpt, 'add_meta_boxes']);
        add_action('save_post_' . Constant::KEY, [$cpt, 'save_meta_boxes']);
        add_action('post_edit_form_tag', [$cpt, 'add_enctype']);

        $download = new Download();
        add_action('init', [$download, 'add_rewrite_rules']);
        add_action('template_redirect', [$download, 'handle']);

        $post = new Post();
        add_filter('single_template', [$post, 'loadCustomTemplate']);

        $assets = new Assets();
        add_action('wp_enqueue_scripts', [$assets, 'enqueue']);

    }
}
