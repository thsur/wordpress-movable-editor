<?php

/*

Plugin Name: Movable Editor
Description: The WYSIWYG content editor wrapped in a movable meta box.
Version:     0.5
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
     * Meta box title
     *
     * See http://codex.wordpress.org/Function_Reference/add_meta_box
     *
     * @var String
     */
    public static $title = '&nbsp;';

    /**
     * Meta box context
     *
     * See http://codex.wordpress.org/Function_Reference/add_meta_box
     *
     * @var String
     */
    public static $context = 'advanced';

    /**
     * Meta box priority
     *
     * See http://codex.wordpress.org/Function_Reference/add_meta_box
     *
     * @var String
     */
    public static $priority = 'high';

    /**
     * Editor id
     *
     * See http://codex.wordpress.org/Function_Reference/wp_editor
     *
     * @var String
     */
    public static $editor_id = 'movable_editor_content';

    /**
     * Editor title
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

        add_action('add_meta_boxes', function () use ($config) {

            foreach ($this->screens as $screen) {

                $config['screen'] = $screen;
                call_user_func_array('add_meta_box', $config);
            }
        });
    }

    /**
     * Apply configuration
     *
     * @return void
     */
    protected function setup() {

        $this->config = array(

            'id'       => Config::$id,
            'title'    => Config::$title,
            'callback' => Config::$callback,
            'screen'   => null,
            'context'  => Config::$context,
            'priority' => Config::$priority
        );

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
