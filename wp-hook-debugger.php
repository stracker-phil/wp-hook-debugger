<?php
/**
 * @formatter:off
 *
 * Plugin name: WP Hook Debugger
 * Description: Debugging plugin that displays a list of all WordPress actions and filters, which have no valid callback function.
 * Author:      Philipp Stracker (divimode.com)
 * Author URI:  https://divimode.com/
 * Version:     1.0.0
 *
 * @formatter:on
 */

add_action( 'wp_footer', 'dm_debug_invalid_hooks', 0 );
add_action( 'admin_footer', 'dm_debug_invalid_hooks', 0 );
add_filter( 'plugin_row_meta', 'dm_debug_usage_hooks', 10, 4 );

define( 'DM_HOOK_DEBUG_PLUGIN', plugin_basename( __FILE__ ) );

/**
 * Scans all registered wp_filter items - that's all actions and filters - and
 * output a list of all hooks that have no valid callback handler.
 *
 * The output is displayed for all users when WP_DEBUG
 * is true. When WP_DEBUG is disabled, the output is displayed only to
 * administrators.
 */
function dm_debug_invalid_hooks() {
	// Do not output debug details in ajax/cron/cli/rest requests.
	if (
		( defined( 'DOING_AJAX' ) && DOING_AJAX )
		|| ( defined( 'DOING_CRON' ) && DOING_CRON )
		|| ( defined( 'WP_CLI' ) && WP_CLI )
		|| wp_is_json_request()
	) {
		return;
	}

	// Only add debug output if an admin is logged in, OR if WP-Debug is enabled.
	if (
		! current_user_can( 'install_plugins' )
		&& ! ( defined( 'WP_DEBUG' ) || ! WP_DEBUG )
	) {
		return;
	}

	$code = [];
	$num  = 1;

	$code[] = '<div id="dm-debug-hooks-box">';
	$code[] = sprintf(
		'<h3>Invalid %s-hooks:</h3>',
		is_admin() ? 'Admin' : 'WP'
	);

	$code[] = '<ul id="dm-debug-hooks">';
	foreach ( $GLOBALS['wp_filter'] as $name => $hook ) {
		foreach ( $hook->callbacks as $prio => $list ) {
			foreach ( $list as $cb ) {
				$valid = is_callable( $cb['function'], false, $fn_name );
				if ( ! $valid ) {
					$code[] = sprintf( '<li>%d. <strong>%s</strong> [prio %s]: <code>%s</code></li>', $num, $name, $prio, $fn_name );
					$num ++;
				}
			}
		}
	}
	$code[] = '</ul>';
	$code[] = '<p>&rarr; <a href="#" onclick="CopyHooksToClipboard();return false">Copy details to clipboard</a></p>';
	$code[] = '<p><em>Note: Not all invalid hooks will cause a PHP warning!<br><br>WordPress (or plugins) might add hooks by default and only load the required function libraries when the relevant hook can be called.<br>For example: WordPress adds some ajax hooks, but will not load the actual ajax handler functions, unless an ajax call is detected.</em></p>';
	$code[] = '</div>';
	$code[] = '<style>';
	$code[] = '#dm-debug-hooks-box, #dm-debug-hooks, #dm-debug-hooks li, #dm-debug-hooks-box a, #dm-debug-hooks h3 {color: #333; font-size: 14px; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif; line-height: 1.4em}';
	$code[] = '#dm-debug-hooks-box {position: relative; z-index: 100000; padding: 10px 20px; margin: 10px; background: #E5E0E0; border: 1px solid #A55; border-left-width: 3px; border-radius: 3px; box-shadow: 0 2px 10px 1px #6008;}';
	$code[] = '#dm-debug-hooks-box h3 {margin: 0; padding: 0; font-size: 20px; color: #000}';
	$code[] = '#dm-debug-hooks {list-style: none; padding: 0; margin: 16px 0;}';
	$code[] = '#dm-debug-hooks li {margin: 4px 0; padding: 4px;}';
	$code[] = '#dm-debug-hooks-box a {color: #6200ea; text-decoration: underline;}';
	$code[] = '#dm-debug-hooks code {padding: 3px 5px 2px 5px; margin: 0 1px; background: rgba(0,0,0,.07); font-size: 14px; color: #600; font-family: Consolas,Monaco,monospace; font-weight: 500}';
	$code[] = '</style>';
	$code[] = '<script>';
	$code[] = 'function CopyHooksToClipboard() {';
	$code[] = 'var r = document.createRange();';
	$code[] = 'r.selectNode(document.getElementById("dm-debug-hooks"));';
	$code[] = 'window.getSelection().removeAllRanges();';
	$code[] = 'window.getSelection().addRange(r);';
	$code[] = 'document.execCommand("copy");';
	$code[] = 'window.getSelection().removeAllRanges();';
	$code[] = 'window.alert("Details were copied into the clipboard");';
	$code[] = '}';
	$code[] = '</script>';

	echo implode( "\n", $code );
}

function dm_debug_usage_hooks( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( $plugin_file !== DM_HOOK_DEBUG_PLUGIN ) {
		return $plugin_meta;
	}

	$usage = [];
	$usage[] = '<i class="dashicons dashicons-info-outline"></i> <strong>How it works</strong>: Enable the plugin. You will see a list of invalid hooks at the bottom of every page.<br>';
	$usage[] = '<i class="dashicons dashicons-info-outline"></i> <strong>Who will see the debug output?</strong> Administrator users will always see the debug output. While <code>WP_DEBUG</code> is enabled, every user can see the debug output.<br>';
	if (defined('WP_DEBUG') && WP_DEBUG) {
		$usage[] = '<i class="dashicons dashicons-warning"></i> <strong>WP_DEBUG is enabled:</strong> Every visitor will see the debug output at the bottom of the page, also on the front end!<br>';
	} else {
		$usage[] = '<i class="dashicons dashicons-info-outline"></i> <strong>WP_DEBUG is disabled:</strong> Only administrator users will see the debug output at the bottom of the page.<br>';
	}
	$usage[] = '<br><strong style="color:#800">Disable this plugin when not using it anymore!</strong>';

	$plugin_meta[] = 'Debugger enabled</div><div style="background:#F001;padding:10px;color:#333e">' . implode( '', $usage );

	return $plugin_meta;
}
