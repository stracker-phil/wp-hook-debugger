# WP Hook Debugger

This is a small debugging plugin that aids in solving the following PHP warning:

```
Warning: call_user_func_array() expects parameter 1 to be
a valid callback, first array member is not a valid class
name or object in /wp-includes/class-wp-hook.php on line 287
```

This warning is triggered, when a plugin or theme tries to hook into a WordPress action or filter but does not provide a valid callback function. It can be difficult to debug this issue, because the warning only tells us, that *a hook* is invalid, but not *which hook* causes the problem.

After activating this plugin, you will see a list of all WordPress hooks, that have an invalid callback function. This list will also include valid hooks, but it helps you to track down the problematic plugin/hook.

## How to install it

1. Click on the green `↓ Code` button above. Choose "Download ZIP" from the menu
2. In WordPress: Open the page **wp-admin | Plugins | Add New** and upload the zip file

*Download the plugins zip file:*

![Download ZIP](https://github.com/stracker-phil/wp-hook-debugger/blob/master/installation-1.png?raw=true)

## How to use it

1. Install and activate the plugin.
2. Disable all other plugins.
3. Load the page that displayed the warning. Scroll to the bottom and look at the "Invalid hooks" section.
4. Activate each plugin individually and check, if the warning appears.
5. When the warning appears: Scroll to the bottom again and look at the "Invalid hooks" section. It will show additional items → those additional hooks are causing problems!

## Screenshots

*While active, the plugin displays inline information on the plugins page:*

![Activated plugin](https://github.com/stracker-phil/wp-hook-debugger/blob/master/screenshot-1.png?raw=true)

*Sample of the debug output generated by the plugin:*

![Sample output](https://github.com/stracker-phil/wp-hook-debugger/blob/master/screenshot-2.png?raw=true)