=== WP Cloud Server ===
Contributors: Designed4Pixels
Donate link: https://wpcloudserver.dev/
Tags: wordpress hosting, cloud servers, cloud hosting, web hosting
Requires at least: 4.8
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: 3.0.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Manage cloud servers from inside your WordPress Dashboard. Create templates to sell servers or websites to your customers using WooCommerce or EDD.

== Description ==

'WP Cloud Server' allows you to manage all of your cloud server accounts from a single, powerful, control panel inside your WordPress Dashboard. But it’s real power is when you combine services together using templates. For example, you can create a template that automatically deploys a DigitalOcean Droplet, connects it to ServerPilot, and installs WordPress.

Support for WooCommerce and Easy Digital Downloads is built-in, so you can create a hosting plan product and attach a template! When a customer purchases the plan and payment completes the services are deployed as defined by the template!

== Built-in Modules ==

The plugin includes built-in modules that provide support for many of the most popular cloud providers and services available today. Once the plugin is installed you can use any service for which you have an account! Modules can be activated and deactivated at will to suit your set-up!

With the current release of the plugin the following modules are built-in.

* DigitalOcean
* Linode
* UpCloud
* Vultr
* Amazon Lightsail
* RunCloud
* ServerPilot
* Ploi
* Cloudways

Overtime, with each release of the plugin we will be adding more modules and additional features.

== Connecting to Services ==

As we said above you can use a combination of modules for which you have an account. For example, to deploy servers with Amazon Lightsail and to connect them to RunCloud would require your own account with both Amazon Lightsail and RunCloud.

Please note that both services may charge a fee for the services that you wish to use! Please make sure that you understand the costs involved when creating templates, etc.

WP Cloud Server connects to services using the official API’s. Each module has a settings page where you can enter the necessary API credentials. Once a valid set of credentials are added and saved you can start using the powerful features built-in to the plugin!

== Privacy Policy ==

As mentioned earlier this plugin connects to your existing cloud service accounts. The plugin saves your API credentials which are only used to send 'Post' or 'Get' requests related to information that you are viewing or commands you activate to manage servers. Your personal API credentials can be deleted whenever you wish from the appropriate settings page, and are never communicated outside of your WordPress site, apart from the API calls mentioned above, and are deleted when you uninstall the plugin.

== Features Overview ==

The plugin allows you set-up exactly the hosting environment that you want by providing the following features;

* Use the set-up wizard to enter your API Keys and SSH Keys for the services you wish to use.
* Modules can be enabled or disabled to match your ideal use case.
* Module specific functionality is only displayed when the appropriate module is activated.
* Modules can interact e.g for connecting a DigitalOcean Droplet to ServerPilot or RunCloud.
* Install WooCommerce or EDD to sell website hosting plans using templates.
* Manually create new servers from inside the WP Admin Dashboard.
* Add startup scripts that can be selected when creating a new server or in templates.
* Add SSH Keys that can be selected when creating a new server.
* Create server templates to allow selling of website hosting plans to customers.
* Enable Server Backups.
* Automatically connect new cloud servers to ServerPilot or RunCloud.
* List full details of servers.
* List full details of server templates.
* Perform reboots, power cycles, shutdowns, or delete servers from the control panel.

== Powerful Module Add-ons ==
 
Over the coming weeks and months we will be actively adding new features to the WP Cloud Server plugin making it a truly powerful cloud server management tool and hosting platform. We will also be adding a steady stream of powerful new features in the form of add-on modules.
 
These modules will provide additional services such as website monitoring, messaging and alert services, email, and even domain names!

== New Features ==

Our aim so far has been to build a powerful control panel that interacts with the services you wish to use. We have focused on providing the most powerful modules, and have been working to create a stable, easy to use, and speedy interface! This has taken longer than intended, but we believe we are nearly there!

We have tried to integrate the most frequently used actions, such as deploying a server, viewing your servers, connecting servers to services like RunCloud, enabling backups, creating templates, etc.

This means that many features are not yet available! Our intention is to provide all actions for each service over the coming releases, either in the FREE plugin or as add-ons! We are aiming where possible for monthly updates.

We are very much focused on our existing users, several have already helped us to tidy-up the functionality for certain use cases. If you are using the plugin and have an urgent need for a feature not yet implemented, or even for a new module, then please contact us and we will see if we can add the feature in the next release!
 
== Installation ==

The plugin is available for download from the WordPress Plugin Directory or via the plugins website. You can then install the plugin from the Plugin page in your WordPress dashboard. Alternatively, you could also upload the plugin folder to your WordPress plugins folder.
 
1. Install the plugin, or upload the plugin folder to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the set-up wizard or manually enter API settings.
4. Start providing website hosting services to your clients!

== Frequently Asked Questions ==
 
= What is 'WP Cloud Server'? =
 
'WP Cloud Server' is a WordPress plugin which allows you to create and manage Cloud Servers, with WordPress installed, from inside your WordPress Admin Dashboard. It uses ServerPilot and DigitalOcean to manage servers and the popular Easy Digital Downloads plugin to sell WordPress Hosting Plans.
 
= Where can I find Support Documentation? =
 
Please visit our website for [Support Documentation](https://wpcloudserver.dev/docs-category/wp-cloud-server/). If you need more in-depth support then you can contact us through our Website, send us an email, or even join us on Social Media.
 
We are always happy to help, and will respond to any problems as quickly as possible – even if we need to update the plugin!
 
= Is WP Cloud Server FREE to use? =
 
The standard version of the 'WP Cloud Server' plugin is available through the WordPress plugin repository, and is completely FREE to use. Please note though that the plugin requires that you have external accounts with DigitalOcean and ServerPilot, whose services may incur a monthly charge!
 
= Are you affiliated with any Cloud Provider? =
 
No. We are a totally independent team of developers who are passionate about creating the best WordPress hosting tools. We have had the pleasure of using many of the services for many years to create and manage WordPress Websites.

= Future Roadmap? =
 
We will be providing frequent updates to the plugin to provide more advanced features. The following features are planned for future releases of the plugin;
 
* Add Manage Server Page for Rebooting, Powering, and Deleting Cloud Servers (Completed).
* Additional short codes for use on the frontend (Server and Website Info Shortcodes Available).
* Add AutoSSL functionality to standalone DigitalOcean Cloud Servers.
* Ability to add a Cloud-Init script for use when creating DigitalOcean Cloud Servers ( e.g. Install WordPress )(Completed).
* Select plugins and themes to be installed on websites.
 
Note: The exact features in each release are subject to change at any time!
 
= Will you be providing add-on modules? =
 
Yes. Over the coming weeks we will be releasing a number of add-on modules that will extend the services that can be used by the WP Cloud Server plugin. Modules currently in development or testing include;

Modules available from our website;

* StatusCake Uptime Monitoring.

Modules in development or testing;

* Brightbox
* Forge
* Netlify
* WordPress Multisite
* Slack
* SendGrid
* DNSimple
 
We have ideas for many more modules!
 
Get early access to these and all future modules as they are released, and lock in the offer price for the life of your account! Please visit our website for details of our [launch offer](https://wpcloudserver.dev/module-launch-offer/)

== Screenshots ==
 
1. Setup Wizard - Introduction
2. Setup Wizard - DigitalOcean API Setting
3. Setup Wizard - ServerPilot API Setting
4. Setup Wizard - SSH Key Setting
5. Setup Wizard - Complete & Save
6. Module Overview
7. Module Settings
8. Module Event Log
9. Module Debug Page
10. List SSH Keys
11. List Managed Servers
12. List Installed Websites
13. List Cloud Servers
14. Install New WordPress Website
15. General Settings
16. Create New Managed Server
17. Create New Cloud Server
18. Add New SSH Key
 
== Changelog ==

= 3.0.8 =
* FIXED: Switching Home Page Settings to Static Page causes PHP error.

= 3.0.7 =
* FIXED: Incorrect sequencing in set-up wizard pages.
* FIXED: Blank pages in set-up wizard pages.

= 3.0.6 =
* NEW: Added the new Ploi Module to Managed Servers.
* NEW: Added functionality for RunCloud to perform a GIT Deploy using GitHub Add-on Module.
* NEW: Added functionality for Startup Scripts to be selected from GIT Repository using GitHub Add-on Module.
* NEW: Added functionality for Vultr Servers to deploy Vultr Apps using the Vultr 'Pro' Add-on Module.
* NEW: Added functionality for menus in the pop-up pages for new Server/App management features.
* NEW: Added the new AWS Lightsail Europe (Stockholm) Region.
* UPDATED: Moved SSH Keys, Hostnames, and Startup Scripts Settings out of General Settings and into each server control panel.
* FIXED: Cleaned up the module overview logic.
* FIXED: Added Ploi to the Set-up Wizard.
* FIXED: Cleaned up the WordPress when Set-up Wizard is active.
* FIXED: When adding new Cloudways Server or App and assigning to an exisitng project always creates as a new project.
* FIXED: Enable Backup checkbox not working when connecting Linode Server to ServerPilot.
* FIXED: UpCloud Hosting Plans not always being deployed.
* FIXED: UpCloud Server IP Address not being displayed correctly in pop-up summary window.
* FIXED: AWS Lightsail module not updating server list correctly.
* FIXED: AWS Lightsail module dispaying incorrect API warning message.
* FIXED: Delay in API Disconnect indication when API credentials deleted.
* FIXED: Delay in DigitalOcean API connecting when configured as first module outside of Setup Wizard.
* FIXED: RunCloud API credentials not deleted when delete requested via settings page.

= 3.0.5 =
* NEW: Added ability to hide inactive modules in module overview page.
* UPDATE: Improved module overview logic to remove the double page load.
* UPDATE: Improved framework for handling web service modules and add-on modules.
* FIXED: ServerPilot App management not using modal to display app data.
* FIXED: ServerPilot Module staying activated when deactivated via module overview.
* FIXED: DigitalOcean Module staying activated when deactivated via module overview.
* FIXED: RunCloud Web Application missing from menu.
* FIXED: RunCloud Web Application PHP errors for server and system user dropdowns.

= 3.0.4 =
* NEW: Added Checkbox to Cloudways template to enable automatic 'Send Email' with login details.
* NEW: Added email to send website and login details to client when Cloudways website is ready.
* FIXED: If Cloudways is the only active module then functionality is disabled due to false 'API Failure'.
* FIXED: Editing Cloudways template may fail to update data and volume sizes.
* FIXED: Client website shortcode still fail to display data correctly.
* FIXED: Client server shortcode displays only the most recent server.
* FIXED: Easy Digital Downloads 'View Order Details' repeatedly displaying empty data.

= 3.0.3 =
* NEW: Added Amazon Lightsail Cloud Provider.
* NEW: Added Pop-up pages for server management.
* NEW: Added System User selection in RunCloud module.
* NEW: Added WP Background Process for API GET commands.
* UPDATE: Added Client tab back for each provider.
* UPDATE: Set default state for modules to inactive to prevent spurious messages.
* FIXED: Fixed setup wizard where activating a selection of modules caused a blank screen.
* FIXED: Fixed admin menus not showing after modules activated through setup wizard.
* FIXED: Website and server shortcodes to fail to display.
* FIXED: Blank pages when clicking menu options.
* FIXED: Checkout Fields not displaying even though selected in template.
* FIXED: PHP errors when opening shortcode settings pages.
* FIXED: HTML elements with duplicate ID's.
* FIXED: Error when saving Amazon Lightsail API settings in set-up wizard.
* FIXED: Numerous fixes and improvemnts for speed and reliability.

= 3.0.2 =
* FIXED: PHP error in dashboard causing possible install failure.
* FIXED: Error when all cloud providers deactivated.

= 3.0.1 =
* FIXED: PHP errors when viewing RunCloud Module pages.
* FIXED: Error that could cause automatic deploy of RunCloud server to fail.
* FIXED: Error that could cause creation of RunCloud web application to fail.
* NEW: Added RunCloud Default Application checkbox.
* NEW: Added option to select install PHP Script Installer inside template.
* NEW: Hostname and Domain used for naming servers when using templates.

= 3.0.0 =
* NEW: Added Vultr Cloud Provider.
* NEW: Added Linode Cloud Provider.
* NEW: Added UpCloud Cloud Provider.
* NEW: Added RunCloud Server Management.
* NEW: Added Cloudways Managed Hosting.
* NEW: Added automatic connection of servers to RunCloud.
* NEW: Restructured underlying framework for new modules.
* NEW: Added API cron tasks to remove delay on user interface upload.
* UPDATE: Added WordPress Admin menus for each service category.
* UPDATE: Structured menus so each service has own page.
* FIXED: Template pages not showing when using WooCommerce.
* FIXED: Problem with site counter not always working in template list.

= 2.2.0 =
* NEW: Added integration with WooCommerce for selling hosting plans.
* NEW: Added more DigitalOcean Droplet types.
* NEW: Added 'Enable Server Backups' option.
* NEW: Added 'Managed Hosting' section for use by 'Cloudways' module.
* NEW: Added 'tabbed' menu for grouping together module specific functions.
* NEW: Added ability to edit templates.
* NEW: Added setting to remove 'Quick Guides' from menu when no longer needed.
* NEW: Added shortcode configuration page for displaying 'Client Websites'.
* NEW: Added 'Enable Server Backups' option to templates.
* UPDATE: Improved the contents of the 'Quick Guides'.
* UPDATE: Restructured menus to make sections more logical.
* UPDATE: Tidied up the look and feel slightly.
* FIXED: Incorrect data displayed in debug pages.
* FIXED: Further improvements to enabling/disabling module logic.
* FIXED: Problem displaying EDD Hostname field at checkout.
* FIXED: Started to move API tasks into background to speed-up page loads.
* FIXED: Numerous Minor Bug fixes.

= 2.1.2 =
* FIXED: Random API error messages after installing and activating plugin.
* FIXED: Disabled API error messages from old interface.
* FIXED: Further improvements to enabling/disabling module logic.
* FIXED: PHP errors when opening ServerPilot Managed Server Template page with API disconnected.

= 2.1.1 =
* NEW: Add startup script for use when deploying servers. Can be selected for manual deploy and in templates.
* NEW: Add server info shortcode tab with checkboxes to select displayed data.
* NEW: Add hostname setup page, allowing automatic incrementing of hostname e.g. hostname1
* NEW: Add 'Client Server Details' page displaying server name against client name and email. 
* UPDATE: Added 'hostname' selection and 'startup script' selection to templates.
* FIXED: Problem with EDD checkout input fields.
* FIXED: Numerous Minor Bug fixes.

= 2.1.0 =
* NEW: Manage Cloud Servers via Pop-up Modal (Reboot, Power On/Off, Delete)
* NEW: Auto Generates Password if no SSH Key set-up, and sends email notification.
* UPDATE: DigitalOcean EDD Checkout tidied up and host-name added.
* FIXED: Create Cloud Server fields greyed out when ServerPilot Module in-active.
* FIXED: Numerous Bug fixes.

= 2.0.0 =
* NEW: New Setup Wizard Added
* NEW: Complete reworking of the User Interface.
* NEW: SSH Key functionality for ServerPilot and DigitalOcean.
* FIXED: Numerous Bug fixes.

= 1.3.0 =
* FIXED: Numerous Small Bug fixes
* NEW: Improved the framework for adding add-on modules

= 1.2.1 =
* FIXED: Numerous Bug fixes
* FIXED: Module Activate/Deactivate Logic more reliable
* FIXED: New Site was sometimes not added to SSL Queue
* UPDATE: Available Services now dependent on API Health Check
* UPDATE: Shared Hosting selection improved
* UPDATE: EDD Meta-Box Dropdowns tidied up
* NEW: EDD Downloads Page has ability to enable/disable Hosting Plan Settings
* NEW: EDD 'No Downloadable Files' message in emails removed for Hosting Plans
* NEW: EDD Email Tags added for 'Domain Name', 'Server Location', 'User Name', and 'User Password'

= 1.2.0 =
* Numerous bug fixes and general tidying up.
* Added new functionality to allow easy integration with add-on modules.
* Layout and UI improvements with functionality now contained in the correct module.
* Added more Ubuntu Linux versions into DigitalOcean Image selection dropdown. More Linux versions to follow!
* Improved functionality for using Serverpilot Servers for Shared Hosting.
* Added Checkbox for enabling AutoSSL in ServerPilot Templates.
* Added Checkbox to allocate a manually created ServerPilot Server for Shared Hosting. 

= 1.1.0 =
* Updated readme.txt.
* Numerous bug fixes and general tidying up.
* Fixed problems with activating/deactivating modules.
* Split 'Server' and 'Templates' into separate pages.
* ServerPilot Module updated to allow creation of 'Apps' manually.
* Public SSH Key capability added to the ServerPilot Module.
* Added 'SSL Queue' to ServerPilot Module to wait for correct DNS configuration.
* Added 'Client Info' short-code.
* DigitalOcean Module updated to autodetect disabled data centres.
* Easy Digital Downloads plugin now only required to sell Hosting Plans.
* Added the ability for 'Templates' to offer 'Server Location' as a user-option at Checkout.
 
= 1.0.2 =
* Updated readme.txt
* Fixed problem with create droplet button in on-boarding dashboard
 
= 1.0.1 =
* Updated readme.txt
* Updated DigitalOcean Module User Meta Script
* Fixed TGM Plugin Activation minor bug causing error message
 
= 1.0.0 =
* First release

== Upgrade Notice ==

= 3.0.0 =
Major release due to major restructuring of framework, menus, and addition of new modules.

= 2.2.0 =
This version alters the template structure to add hostnames and restructures the menus.