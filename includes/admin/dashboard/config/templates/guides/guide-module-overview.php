<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<p><?php _e('The \'Module Overview\' section provides a view of the modules that are available for use with the plugin. The standard FREE plugin, if used standalone, has 8 modules built-in including, DigitalOcean, Vultr, Linode, UpCloud, AWS Lightsail, ServerPilot, RunCloud, and Cloudways. Additional add-on modules can be installed including cloud providers and web services that will also be visible in this section.', 'wp-cloud-server' ); ?></p>

<h2><?php _e('Installed Modules', 'wp-cloud-server' ); ?></h2>
<p><?php _e( 'The \'Installed Modules\' menu option, the default view, lists all of the modules installed and gives an easy to understand overview of what services are avaialble, what modules are active, and whether the API connections are valid.', 'wp-cloud-server' ); ?></p>
<p><?php _e( 'If a service is active and you don\'t need it then the \'deactivate\' option will remove the module from all menus and drop-down lists, etc. Note though that deactivating some modules may result in error messages if, for example, no cloud provider module is active but is required. Also some services will disappear from menus, etc.', 'wp-cloud-server' ); ?></p>

<h2><?php _e('Modules', 'wp-cloud-server' ); ?></h2>
<p><?php _e( 'The \'Modules\' menu, lists all active modules. Clicking on a modules name will take you to the \'Settings\' page for that module which allows API credentials to be managed, there are tabs for event logs, and, if enabled, the debug tab, which provides visibility of API responses for the module and queues.', 'wp-cloud-server'); ?></p>