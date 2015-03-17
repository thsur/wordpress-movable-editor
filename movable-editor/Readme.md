
# WordPress Movable Editor

Change the position of WordPress' WYSIWYG content editor by moving it around like any other element on an editing screen. 

## What it does

By default, WordPress' content editor isn't rendered inside a movable box, so it's fixed in place. This plugin puts one around it. 

## What it really does

Technically, it replaces the default editor with a new instance of it. 

Currently, this also changes the editor's distraction free mode back to fullscreen mode. 

Limitations: Doesn't work with the distraction free, but with fullscreen mode. This is/seems to be a limitation of WP. How to get it work is beyond the scope of this Readme. Another time/place/idea.

## Installation

## Usage

## Configuration   


Unlike most other elements on an editing screen, it can't be moved around, its position can't change. This plugin fixes that. 


It can't be moved around, nor can 



Swaps WYSIWYG content editor against a new one wrapped in a movable meta box.

By default, 

## Configuration

It should render first after activation bust this depends on your current scrren laoyot.

#### Closing The Gap

`$context = 'normal'` // default value
`$context = 'advanced'` // might cause gap

When you first install it, you might see a 50px gap between the title field and whatever follows it (probably the editor itself). This gap is an empty area to add meta boxes to. Just drag the box following the gap into it, and you should be fine. You only need to do this once (right after activating the plugin). The gap isn't by design, but ...

If you want to close it globally, try our ... plugin.

NORMAL CASE:

### Editor Is Rendered Last, Not First

Let's assume you already have some meta boxes below the default editor, maybe pulled in from the right colum. When you now activate the plugin, leaving it's `$context` setting as is, the editor renders after all other meta boxes in its column. Just drag it in it's desired position.
