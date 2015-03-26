<?php

/*

Plugin Name: Movable Editor
Description: The WYSIWYG content editor wrapped in a movable meta box.
Version:     1.0
Plugin URI:  https://github.com/pontycode/wordpress-movable-editor/
Author:      Thsurs
Author URI:  https://github.com/pontycode
License:     GPL v2 or later

Copyright © 2015 Pontycode

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

*/

namespace MovableEditor;

define(__NAMESPACE__.'\VERSION', '1.0');

/**
 * Meta box & editor configuration
 *
 * Though the only thing you might want
 * to change is the title.
 *
 * Example
 * -------
 *
 * To configure the title of the editor's surrounding box,
 * add the following line to your theme's function.php:
 *
 * \MovableEditor\Config::$title = __('Content');
 *
 */
class Config {

    /**
     * Editing screens to operate on
     *
     * @var String
     */
    public static $screens = array('post', 'page');

    /**
     * Whether or not to include Custom Post Types
     * to the list of allowed screens.
     *
     * @var Boolean
     */
    public static $include_cpts = true;

    /**
     * Screens to exclude
     *
     * Use this if you want to include Custom Post Types,
     * but exclude some of them.
     *
     * @var Array
     */
    public static $exclude = array();

    /**
     * Meta box id
     *
     * See http://codex.wordpress.org/Function_Reference/add_meta_box
     *
     * @var String
     */
    public static $id = 'movable_editor';

    /**
     * Meta box title(s)
     *
     * To give the box a title based on a specific post type,
     * add it as a key:
     *
     * \MovableEditor\Config::$title = array(
     *
     *     'default' => '&nbsp;',
     *     'post'    => 'Post Content',
     *     'page'    => 'Page Content'
     * );
     *
     * @var Array
     */
    public static $title = array('default' => '&nbsp;');

    /**
     * Editor id
     *
     * If set to 'content' (which is the default value), the editor
     * is given the same id the default editor has, so it goes by
     * as the normal post body.
     *
     * Which means all updating action is taken care of for us,
     * including wp.autosave.
     *
     * So if you set it to something else, don't forget to register
     * some function to WordPress' 'save_post' action hook.
     *
     * @var String
     */
    public static $editor_id = 'content';

    /**
     * Editor configuration
     *
     * See http://codex.wordpress.org/Function_Reference/wp_editor
     *
     * @var String
     */
    public static $editor_config = array();

    /**
     * Meta box content callback
     *
     * See below for default implementation.
     *
     * @var String
     */
    public static $callback;
}

/**
 * Initialize an editor
 *
 * See http://codex.wordpress.org/Function_Reference/add_meta_box
 *
 * @var Object
 */
Config::$callback = function ($post) {

    // See http://codex.wordpress.org/Function_Reference/wp_editor
    wp_editor($post->post_content, Config::$editor_id, Config::$editor_config);
};

/**
 * Replace the WYSIWYG content editor by a new instance
 * wrapped in a movable meta box.
 */
class MovableEditor {

    /**
     * Editing screens to work with
     *
     * @var Array
     */
    protected $screens = array();

    /**
     * Meta box config
     *
     * @var Array
     */
    protected $config = array();

    /**
     * Editor id
     *
     * @var Integer
     */
    protected $editor_id = null;

    /**
     * Meta box title(s)
     *
     * @var Array
     */
    protected $title = array();

    /**
     * What base screen we are on, if any.
     *
     * @return String|false
     */
    protected function getCurrentScreen() {

        $screen = get_current_screen();

        if (!($screen && property_exists($screen, 'base'))) {

            return false;
        }

        return $screen->post_type ? $screen->post_type : $screen->base;
    }

    /**
     * Whether the given screen is in the list of allowed ones.
     *
     * @param  String
     * @return Boolean
     */
    protected function isScreenAllowed($screen) {

        return in_array($screen, $this->screens);
    }

    /**
     * Remove default editor
     *
     * @return void
     */
    protected function removeEditor() {

        foreach ($this->screens as $screen) {

            remove_post_type_support($screen, 'editor');
        }
    }

    /**
     * Add editor as metabox
     *
     * @return void
     */
    protected function addEditor() {

        $config = $this->config;

        // Add editor

        add_action('add_meta_boxes', function () use ($config) {

            $screen = $this->getCurrentScreen();

            if (!$this->isScreenAllowed($screen)) {

                return;
            }

            $config['screen'] = $screen;
            $config['title']  = isset($this->title[$screen]) ? $this->title[$screen]
                                :  $this->title['default'];

            call_user_func_array('add_meta_box', $config);
        });

        add_action('admin_enqueue_scripts', function () use ($config) {

            $screen = $this->getCurrentScreen();

            if (!$this->isScreenAllowed($screen)) {

                return;
            }

            wp_enqueue_script(

                'movable_editor',
                plugin_dir_url(__FILE__).'movable_editor.js',
                array('jquery', 'jquery-ui-sortable'),
                \MovableEditor\VERSION,
                true
            );

            wp_localize_script('movable_editor', 'movable_editor_config', array(

                'editor_id'   => $this->editor_id,
                'meta_box_id' => $config['id']
            ));
        });
    }

    /**
     * Apply configuration
     *
     * @return void
     */
    protected function setup() {

        $this->config = array( // Important: Don't change key order (since config is
                               // passed to WP as a non-associative array later on).

            'id'       => Config::$id,
            'title'    => null,
            'callback' => Config::$callback,
            'screen'   => null,

            // We want the editor to appear first, just like
            // the default editor does.
            //
            // We _could_ set the context to 'advanced' instead,
            // but this would cause an empty sortable area to be
            // rendered below the 'title' field, resulting in the
            // exact same gap described here:
            // http://pytest-commit.git.net/wp-trac/dsc61814.html

            'context'  => 'normal',
            'priority' => 'high'
        );

        $this->editor_id = Config::$editor_id;

        if (is_array(Config::$title)) {

            $this->title = Config::$title;
        }

        if (!isset($this->title['default'])) {

            $this->title['default'] = 'nbsp;';
        }

        $screens = Config::$screens;

        if (Config::$include_cpts) {

            $cpts    = get_post_types(array('_builtin' => false));
            $screens = array_merge($screens, $cpts);
        }

        foreach (Config::$exclude as $slug) {

            $exclude = array_search($slug, $screens);

            if ($exclude !== false) {

                unset($screens[$exclude]);
            }
        }

        $this->screens = $screens;
    }

    /**
     * Init
     */
    public function __construct() {

        $this->setup();
        $this->removeEditor();
        $this->addEditor();
    }
}

if (is_admin()) {

    add_action('admin_init', function () {

        new MovableEditor();

    });
}