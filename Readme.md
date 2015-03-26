
# Movable Editor

Change the position of WordPress' WYSIWYG content editor by moving it around like any other element on an editing screen. 

## What It Does

By default, WordPress' content editor isn't rendered inside a movable box, so it's fixed in place. This plugin puts a box around it to set it free to float.

## Limitations

Technically, the plugin replaces the default with a *new* editor that just _pretends_ to be the former to inherit its behaviour (like auto-saving). 

This has its limitations, though: As it currently stands, WordPress' new distraction free mode works in the default editor only, not in custom ones. Any non-default editor will, however, fall back to the old full screen mode in terms of distraction free writing, and so will this one. To some, this might be a feature.   

## Installation

Download and unpack, then move the folder 'movable-editor' into your 'plugins' folder. Head over to your WordPress installation and activate the plugin in the admin area.

## Usage

Just move the editor around, that's all. It will still sit where you left it next time you come around. Though you might want to [configure](#configuration) it a bit. 

## Configuration   

Configuration happens solely in your theme's `functions.php`. Though you don't need to configure anything. Actually, there isn't really much to configure but:  
* the screens on which the editor should show up (post, page, custom post type by default)
* which name to give the box the editor is in

Anyway, fire up an editor, load your functions.php, and copy and paste the following sample configuration into it. Adjust it to suit your needs.

For brevity, we'll assume the plugin is loaded and active, so we won't check for that (see this nice [write-up](http://queryloop.com/how-to-detect-if-a-wordpress-plugin-is-active/) on _QueryLoop_ on some ways to do it, though).

```PHP
if (is_admin()) {

    // Operate on post and page screens (default setting)
    \MovableEditor\Config::$screens = array('post', 'page');

    // Include custom post types (default setting)
    \MovableEditor\Config::$include_cpts = true; 

    // Exclude a custom post type (or more of them)
    \MovableEditor\Config::$exclude = array('acme');

    // Change the default title of the editor's meta box
    \MovableEditor\Config::$title['default'] = 'Content';

    // Add a different title per screen type
    \MovableEditor\Config::$title['post'] = 'Post Content';
    \MovableEditor\Config::$title['page'] = 'Page Content';
    \MovableEditor\Config::$title['acme'] = 'Custom Post Type Content';
}
```

If you have browsed the plugin's source code, you might have seen some other settings available for configuration. This is because this plugin can also be used as a blueprint to set up your own custom editor. Please review the configuration class carefully again before doing so. For example, you might need to implement your own data handling.     

##  Packaging Slip

Boxing an editor in is no big deal. But moving it, on the other hand, is a delicate thing:

> Once instantiated, the WYSIWYG editor cannot be moved around in the DOM. What this means in practical terms, is that you cannot put it in meta-boxes that can be dragged and placed elsewhere on the page.
>
> (https://codex.wordpress.org/Function_Reference/wp_editor)

Which, in fact, is exactly what we do. Putting an editor in a box and moving it around and all. Issuing file requests behind the scenes because parts of it need to be recreated each time it has been moved. So it doesn't lose its styles. Or its content. A delicate thing, indeed.

So in a larger system, you might not want your users to get too crazy about dragging the editor around. Though they should be safe to do so, you might want to review our [Global Meta Box Order]() plugin to lock things down a bit. 

## License

GNU GPL v2 or later