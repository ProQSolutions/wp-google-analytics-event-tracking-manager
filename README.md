# Event Tracking Manager Plugin for Google Analytics

This plugin is exactly what it says it is: a plugin for managing Google
Analytics event tracking in WordPress.

## Setup Notes

This plugin does not include Google Analytics tracking code. You will need to
add it yourself for this plugin to work. This plugin expects `ga` to be defined
globally.

## Features

* Configure events to track using CSS query syntax (eg .classname, #idname,
etc...)
* Hook on to standard javascript events such as click, mouseenter, etc.
* Hook on to custom javascript events
* Specify if event binding should be first
* Specify if event binding should be done through a delegate
* Custom event "in_view" for tracking when elements are visible on screen

## To Do

* Add support for opening links in new tab
* Add support for more replace strings (currently only supports: {text} {href}
{class} {id})
* Add more documentation to admin page of plugin
* Add support for form submission events
* Add option to include Google Analytics tracking code

## Someday

* Incorperate Google Measurement Protocol to track events from PHP side and
give user options to hook on WP filters and actions.