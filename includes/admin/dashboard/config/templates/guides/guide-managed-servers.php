<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<p><?php _e('The \'Managed Servers\' section allows you to manage your cloud servers using a server management service. The FREE version of the plugin allows connection to ServerPilot, RunCloud, or Ploi, allowing new servers to be connected and applications to be installed and configured.', 'wp-cloud-server'); ?></p>

<h2><?php _e('Dynamic Admin Menus', 'wp-cloud-server'); ?></h2>

<p><?php _e('For example, if you just have the \'ServerPilot\' module activated then the \'Managed Servers\' menu will take you straight to the ServerPilot control panel. But if you have activated \'ServerPilot\' and \'RunCloud\' then you will see these listed in a sub-menu.', 'wp-cloud-server'); ?></p>

<p><?php _e('If you are using other modules and don\'t need any cloud providers for your use case, and have deactivated them all, then the \'Managed Servers\' menu will be hidden!', 'wp-cloud-server'); ?></p>

<h2><?php _e('Server Management Control Panel', 'wp-cloud-server'); ?></h2>

<p><?php _e('You are likely viewing this page having clicked the support menu \'Quick Guide\' menu in a Server Management control panel. We will assume that this is one of the first menu links that you have clicked, so we will give you an overview of all functionality.', 'wp-cloud-server'); ?></p>

<h2><?php _e('Main Menu', 'wp-cloud-server'); ?></h2>

<p><?php _e('Each Server Management service offers different services that are acessible from the main menu in the control panel. Even though the menu items vary we have attempted to keep the same grouping. You should see menu blocks labelled as \'Manage\', \'Settings\', and \'Support\'. If you have either \'WooCommerce\' or \'Easy Digital Downloads\' plugins installed and activated then you will also see a \'Hosting\' menu.', 'wp-cloud-server'); ?></p>

<p><?php _e('Let\'s start by looking at each menu in turn.', 'wp-cloud-server'); ?></p>

<h3><?php _e('Manage Menu', 'wp-cloud-server'); ?></h3>

<p><?php _e('This menu gives access to the server admin pages for the server management provider. This menu will vary depending on the service being used. The menu links will as close as possible mimic the options in the Cloud Providers own dashboard, and as such will include options like \'Apps\'. \'Databases\', \'mojitoring\', etc.', 'wp-cloud-server'); ?></p>

<p><?php _e('Each page opened from this menu will default to a summary page displaying a list of exisitng services, such as servers, etc. If we have implemented the ability to \'Add\' a service, then you will see a tab indicating this. So for manually connecting a server you will see a tab labelled \'+ Add Server\'.', 'wp-cloud-server'); ?></p>

<p><?php _e('The power of the \'Managed Server\' function is the automation. You can select a cloud provider and the configuration for the server management service, and when you click \'Create Server\' everything is deployed automatically.', 'wp-cloud-server'); ?></p>

<p><?php _e('If you selected \'DigitalOcean\' as the cloud provider and \'ServerPilot\' for server management then the plugin will notify ServerPIlot that a new server is being connected. It will then automatically deploy the DigitalOcean server and launch the ServerPilot install script which connects to ServerPilot to install the server software and WordPress. No stress and no hassle!', 'wp-cloud-server'); ?></p>

<p><?php _e('In the summary view you will see details of the service e.g. server name, id, status. At the end of each row is a manage link which if clicked will open a pop-up modal providing full management capabilities for that service. Taking servers as an example this will include deleting a server, and much more. Each release of the plugin will see more features added.', 'wp-cloud-server'); ?></p>

<h3><?php _e('Settings Menu', 'wp-cloud-server'); ?></h3>

<p><?php _e('This menu is for managing useful resources. The items saved here are available across all of the Server Management Providers. The menu options are \'SSH Keys\' and \'Startup Scripts\'. These are probably pretty self explanatory.', 'wp-cloud-server'); ?></p>

<p><?php _e('The \'SSH Keys\' page allows you to view and save public SSH Keys. When creating templates or deploying a cloud server manually you can select an SSH Key to install on the server.', 'wp-cloud-server'); ?></p>

<p><?php _e('The \'Startup Script\' page allows you to view, save and edit startup scripts. These can be bash scripts or cloud-init scripts that are uploaded to the server during creation and are executed during first boot.', 'wp-cloud-server'); ?></p>

<p><?php _e('Once again if you have \'WooCommerce\' or \'EDD\' activated you will see another menu item called \'Hostnames\'. This setting is currently only used when creating templates for selling web hosting plans. It allows you to create a dynamic hostname that can have an integer incremented as each server is deployed. You can even set a domain name, the protocol, and a port e.g. 8443 for users to login to their server with!', 'wp-cloud-server'); ?></p>

<h3><?php _e('Support Menu', 'wp-cloud-server'); ?></h3>

<p><?php _e('We\'ll mention this menu item for completeness! But it takes you here, to this page, so you already know this one. I will say though that if you\'ve read this page, and understand it\'s content, you can hide it through the \'General Settings\' menu.', 'wp-cloud-server'); ?></p>

<h3><?php _e('Hosting Menu', 'wp-cloud-server'); ?></h3>

<p><?php _e('This menu is displayed if you have \'WooCommerce\' or \'Easy Digital Downloads\' activated. It has two menu options, \'Templates\' and \'Clients\'.', 'wp-cloud-server'); ?></p>

<h4><?php _e('Website Hosting Plan Templates', 'wp-cloud-server'); ?></h4>

<p><?php _e('\'Templates\' are a very powerful feature built-in to the plugin. A \'Template\' is a way of grouping together a set of server features that can be selected inside a \'WooCommerce\' or \'Easy Digital Downloads\' product page, and can then be sold to clients or customers. When a server hosting plan is sold and payment has been confirmed the \'Template\' is used to automatically create the server. Features like SSH Keys, startup scripts, server backups can all be selected.', 'wp-cloud-server'); ?></p>

<p><?php _e('You can create any number of \'Templates\' and view them in the \'Templates\' page via the \'Hosting\' menu, where you can manage and delete templates. Each \'Templates\' row includes a \'Sites\' field that shows how many web sites have been created using the template.', 'wp-cloud-server'); ?></p>

<h4><?php _e('Client Details', 'wp-cloud-server'); ?></h4>

<p><?php _e('The final menu item is called \'Clients\' and links customers details to hosting plans, so that you can see at a glance which clients have what hosting plans. This is basic at the moment but we have massive plans for this in the future!', 'wp-cloud-server'); ?></p>
