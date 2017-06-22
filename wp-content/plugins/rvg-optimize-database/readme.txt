=== Optimize Database after Deleting Revisions ===
Contributors: CAGE Web Design | Rolf van Gelder
Donate link: http://cagewebdev.com/index.php/donations-odb/
Plugin Name: Optimize Database after Deleting Revisions
Plugin URI: http://cagewebdev.com/index.php/optimize-database-after-deleting-revisions-wordpress-plugin
Tags: database, delete, revisions, optimize, post, posts, page, pages, clean, clean up, trash, spam, trashed, spammed, database size, scheduler, transients, unused tags, pingback, trackback, unix cron tab, crontab, multisite
Author URI: http://cagewebdev.com
Author: CAGE Web Design | Rolf van Gelder, Eindhoven, The Netherlands
Requires at least: 2.8
Tested up to: 4.8
Stable tag: 4.2.2
Version: 4.2.2
License: GPLv2 or later

== Description ==

This plugin is a 'One Click' WordPress Database Cleaner / Optimizer.

= Main Features =
* Deletes redundant revisions of posts and pages (you optionally can keep an 'x'-amount of the most recent revisions)
* Deletes trashed posts, pages and comments (optional)
* Deletes spammed comments (optional)
* Deletes unused tags (optional)
* Deletes 'expired transients' (optional)
* Deletes 'pingbacks' and 'trackbacks' (optional)
* Deletes 'orphan postmeta items'
* Optimizes the database tables (optionally you can exclude certain tables, or even specific posts/pages, from optimization)
* Creates a log file of the optimizations (optional)
* Optimization can be scheduled to automatically run once hourly, twice daily, once daily or once weekly at a specific time (optional)
* 'Optimize DB (1 click)' link in the admin bar (optional)
* 'Optimize Database' Icon in the admin menu (optional)
* MULTISITE compatible: optimizes all sites in the network with one click

= Settings =
You can find the settings page in the WP Admin Panel &raquo; Optimize Database -or- via the WP Admin Panel &raquo; Optimize Database icon (depends on settings)

= Excluding specific posts/pages from deleting revisions =
If you want to keep revisions for a specific post/page (no matter what the other settings are), create a custom field named 'keep_revisions' for that post/page and give it the value 'Y'<br>

= Starting the Optimization =
You can start the Optimization in the WP Admin Panel &raquo; Optimize Database.<br>
Note: if you use the Scheduler the Optimization will run automatically!<br>
Note: you also can click the 'Optimize DB (1 click)' link in the admin bar (if enabled)

= Multisite Support =
* Install the plugin as Network Administrator (Network Admin &raquo; Plugins)
* 'Network Activate' the plugin
* You only can configure and run the plugin on the main network site, but it will optimize ALL the sub-sites too!

= Running the plug in from a Unix crontab =
In case you cannot use WPCron, but you can edit the Unix crontab:<br><br>
Create a .php file, in the root directory of your site, with:<br>
&lt;?php<br>
define('RUN_OPTIMIZE_DATABASE', true);<br>
require_once('wp-load.php');<br>
?&gt;<br><br>
Then, start the .php file from your crontab!<br>

= Supported languages =
* Belarus [be_BY] - translated by Natasha - https://www.ustarcash.com/ - keyword UStarCash
* Danish [da_DK] - translated by Alexander Leo-Hansen - http://alexanderleohansen.dk
* Dutch [nl_NL] - translated by Rolf van Gelder, CAGE Web Design - http://cagewebdev.com
* English [en_US] - translated by Rolf van Gelder, CAGE Web Design - http://cagewebdev.com
* German [de_DE] - translated by the.mnbvcx
* Indonesian [id_ID] - translated by ChameleonJohn.com
* Italian [it_IT] - translated by Fabio Marzocca
* Persian [fa_IR] - translated by Milad Mordi, http://seodaramal.ir
* Portuguese [po_BR] - translated by Rede Sul Mineira de Comunicações
* Russian [ru_RU] - translated by Vadim Sokhin, PluginZone - http://pluginzone.ru
* Serbian [sr_RS] - translated by Ogi Djuraskovic, FirstSiteGuide  - http://firstsiteguide.com
* Swedish [sv_SE] - translated by P.E.
* Turkish [tr_TR] - translated by Keremcan Buyuktaskin
* Ukranian [uk_UA] - translated by Anna Goriacha, Coupofy - http://coupofy.com

= Author =
CAGE Web Design | Rolf van Gelder, Eindhoven, The Netherlands - http://cagewebdev.com - http://rvg.cage.nl

= Plugin URL =
http://cagewebdev.com/index.php/optimize-database-after-deleting-revisions-wordpress-plugin

= Download URL =
http://wordpress.org/plugins/rvg-optimize-database/

= Disclaimer =
NO WARRANTY, USE IT AT YOUR OWN RISK!

= Plugins by CAGE Web Design | Rolf van Gelder =
WordPress plugins created by CAGE Web Design | Rolf van Gelder<br>
http://cagewebdev.com/index.php/wordpress-plugins/

== Installation ==

= Single site =
* Upload the Plugin to the `/wp-content/plugins/` directory
* Activate the plugin in the WP Admin Panel &raquo; Plugins
* Change the settings (if needed) in the WP Admin Panel &raquo; Settings &raquo; Optimize Database -or- via the WP Admin Panel &raquo; Optimize Database icon (depends on settings)

= Multisite =
* Install the plugin as Network Administrator (Network Admin &raquo; Plugins)
* 'Network Activate' the plugin
* The settings are the same for ALL sites in the network, so if you change a setting for one site, it will be used for all other sites.
* If you run the plugin from any of the sites, it will cleanup ALL the sites in the network!

== Changelog ==
= 4.2.2 [04/06/2017] =
* NEW: Indonesian (id_ID) translation added

= 4.2.1 [09/23/2016] =
* CHANGE: fixed some (innocent) warnings

= 4.2.0 [09/16/2016] =
* NEW: Added a 'monthly' schedule to the scheduler

= 4.1.10 [07/15/2016] =
* BUG FIX: Bug fix for 4.1.9

= 4.1.9 [07/15/2016] =
* CHANGE: 'Running indicator' added

= 4.1.8 [06/24/2016] =
* CHANGE: Reverted back to 4.1.6 due to some problems. Sorry!

= 4.1.7 [06/23/2016] =
* CHANGE: Orphaned media items will be deleted too now

= 4.1.6 [05/20/2016] =
* NEW: Belarus (be_BY) translation added (thanks Natasha!)

= 4.1.5 [03/11/2016] =
* NEW: Turkish (tr_TR) translation added (thanks Keremcan Buyuktaskin!)

= 4.1.4 [03/02/2016] =
* NEW: Custom field 'keep_revisions', for excluding the deletion of revisions for specific posts/pages
* NEW: Run the optimization from a Unix crontab (if possible)

= 4.1.3 [02/22/2016] =
* BUG FIX: Saving settings didn't work well for multi-sites

= 4.1.2 [02/14/2016] =
* NEW: Brasilian Portuguese (po_BR) translation added
* NEW: Network: True added to the plugin header 

= 4.1.1 [01/17/2016] =
* CHANGE: Use can use the 'Delete revisions older than' AND 'Keep max number of revisions' options at the same time! 

= 4.1 [01/14/2016] =
* NEW: New option: delete revisions older than...
* NEW: Persian translation (fa_IR) added
* CHANGE: For multi-site installations you only can use the plugin on the Main site, but all other sites will be cleaned too.
* CHANGE: Some minor updates / changes

= 4.0.3 [12/17/2015] =
* BUG FIX: Bug fix for Cron Schedules
* BUG FIX: Un-installation generated an error
* CHANGE: Some minor updates / changes

= 4.0.2 [11/28/2015] =
* BUG FIX: Settings weren't saved correctly for MultiSites

= 4.0.1 [11/27/2015] =
* NEW: New option: optimize InnoDB tables too
* BUG FIX: Localization fixed for extra cron schedules
* BUG FIX: De-activation issue on update fixed

= 4.0 [11/22/2015] =
* NEW: Total rewrite of the code (OO code)
* CHANGE: Many changes and bug fixes

= 3.5.1 [11/01/2015] =
* BUG FIX: Views are skipped from the optimization

= 3.5 [10/24/2015] =
* CHANGE: Stylesheet is only loaded on required admin pages

= 3.4.9 [10/23/2015] =
* BUG FIX: Bug fixed for deleting unused tags

= 3.4.8 [09/14/2015] =
* BUG FIX: Bug fixed for deleting transients

= 3.4.7 [08/30/2015] =
* BUG FIX: Transients are now deleted using the delete_transient() / delete_site_transient() functions

= 3.4.6 [06/21/2015] =
* NEW: Danish translation (da_DK) added

= 3.4.5 [06/07/2015] =
* NEW: Russian translation (ru_RU) added

= 3.4.4 [06/03/2015] =
* NEW: German translation (de_DE) added

= 3.4.3 [06/01/2015] =
* NEW: Swedish translation (sv_SE) added

= 3.4.2 [05/10/2015] =
* BUG FIX: fixed a bug (options didn't get saved in old multisite installs)

= 3.4.1 [04/26/2015] =
* BUG FIX: fixed a bug for the excluded tables (didn't work anymore)

= 3.4 [04/24/2015] =
* NEW: Ukrainian translation (uk_UA) added
* CHANGE: updates for Dutch and Italian translations
* CHANGE: a new, fresh look!
* CHANGE: a better way to show the EXCLUDED tables on the settings page

= 3.3.1 [04/08/2015] =
* BUG FIX: fixed a bug with the scheduler (it didn't clean everything)

= 3.3 [03/27/2015] =
* NEW: MULTISITE support added (second try ;-))
* CHANGE: various minor improvements
* BUG FIX: sub-sites are accessible again (in the admin panel)

= 3.2.1 [03/18/2015] =
* CHANGE: due to problems, I removed the MULTISITE support again for now... Sorry about that!

= 3.2 [03/18/2015] =
* NEW: MULTISITE support added
* CHANGE: various minor improvements

= 3.1.4 [03/01/2015] =
* NEW: italian language support [it_IT] added (thanks Fabio Marzocca!)
* NEW: optimization timer
* CHANGE: from now on InnoDB tables are skipped from optimization
* CHANGE: various minor improvements
* BUG FIX: removed strtolower while fetching database properties

= 3.1.3 [02/21/2015] =
* NEW: showing the icon in the admin menu is now configurable via the settings page

= 3.1.2 [02/20/2015] =
* BUG FIX: fixed the link to the settings page

= 3.1.1 [02/20/2015] =
* NEW: added plugin banners and icons
* NEW: added a new admin menu item with icon ('Optimize Database')
* CHANGE: moved the language files to the '/language' directory
* CHANGE: moved the styles to the '/css' directory
* CHANGE: credit removed from the front-end pages

= 3.1 [01/29/2015] =
* NEW: deletion of pingbacks and trackbacks (optional)
* CHANGE: link to settings page in the main plugins page

= 3.0 [12/28/2014] =
* BUG FIX: tags only used in scheduled posts won't be deleted anymore (thanks Michael!)

= 2.9 [10/22/2014] =
* NEW: plugin has been fully localized
* NEW: dutch language support [nl_NL] added
* NEW: serbian language support [sr_RS] added

= 2.8.3 [09/10/2014] =
* CHANGE: using the WP jQuery libs now (not from googleapis anymore)
* BUG FIX: check_admin_referer() fixed (gave a debug warning)

= 2.8.2 [08/01/2014] =
* BUG FIX: all problems with getting the table names should be fixed now!

= 2.8.1 [07/31/2014] =
* CHANGE: changed the 'edit_themes' capability back to 'administrator'-role
* BUG FIX: no table names beside checkboxes (rewrote it, so it should work for every site now)

= 2.8 [07/30/2014] =
* CHANGE: 'administrator'-role changed to 'edit_themes'-capability
* BUG FIX: no table names beside checkboxes

= 2.7.9 [05/02/2014] =
* BUG FIX: some minor bug fixes (thanks Mike!)

= 2.7.8 [05/01/2014] =
* CHANGE: replaced depreciated MySQL queries (from WP 3.9 / PHP 5.5)

= 2.7.7 [02/16/2014] =
* BUG FIX: made jQuery also https compatible

= 2.7.6 [01/16/2014] =
* BUG FIX: empty lines removed from output (gave problems with some RSS feeds)

= 2.7.5 [12/13/2013] =
* CHANGE: two queries optimized for better performance

= 2.7.4 [12/11/2013] =
* BUG FIX: added 'backticks' around the database name in a few queries

= 2.7.3 [12/09/2013] =
* BUG FIX: deleted some CR/LF's from the end of the plugin *sigh*

= 2.7.2 [12/09/2013] =
* BUG FIX: forgot to delete a debug item... oops! sorry!

= 2.7.1 [12/09/2013] =
* BUG FIX: query and depreciated item (mysql_list_tables) fixed

= 2.7 [12/06/2013] =
* NEW: deletion of expired transients (optional)

= 2.6 [07/22/2013] =
* NEW: deletion of unused tags (optional)

= 2.5.1 [05/24/2013] =
* BUG FIX: some short tags removed
* CHANGE: schedule time is only relevant and therefore only shown for 'daily' and 'weekly' schedules
* NEW: option to turn the '1-click' button in the admin bar on/off

= 2.5 [05/24/2013] =
* NEW: you can set a time (hour) for the scheduler to run (thanks to frekel)
* NEW: '1-click run button' in the admin bar (thanks to JB ORSI)

= 2.3.1 [05/03/2013] =
* BUG FIX: fixed a problem with 'invalid header' (during installation) 

= 2.3 [04/26/2013] =
* BUG FIX: fixed the 'Stealing Cron Schedules' issue 

= 2.2.9 [04/10/2013] =
* BUG FIX: bug fix for the 'check all NON-WordPress tables' link

= 2.2.8 [03/19/2013] =
* BUG FIX: bug fix for deleting Post Orphans

= 2.2.7 [03/18/2013] =
* NEW: 'Orphan Post items' (like 'Auto Drafts') will be automatically deleted too now (thanks to: 0izys)

= 2.2.6 [03/05/2013] =
* Text change: 'logging on' changed to 'keep a log' (thanks to: Neil Parks)
* NEW: number of orphans deleted now also shown in the log file
* NEW: 'Go To Optimizer' button on settings page (thanks to: RonDsy)

= 2.2.5 [02/20/2013] =
* Bug fix: fixed an (innocent) PHP warning (in error.log)

= 2.2.4 [02/12/2013] =
* Bug fix: error corrected in readme.txt file

= 2.2.3 [02/09/2013] =
* Bug fix: fixed an (innocent) PHP warning (in error.log)

= 2.2.2 [01/20/2013] =
* Bug fix: deleting of postmeta orphans didn't work correctly

= 2.2.1 [01/17/2013] =
* Bug fix: fixed some debug warnings

= 2.2 [01/11/2013] =
* NEW: 'Orphan Postmeta items' will be automatically deleted
* NEW: the possibility to exclude tables from Optimization (for instance for 'heavy traffic' tables)

= 2.1 [01/04/2013] =
* Bug fix: keeping a maximum number of revisions didn't work correctly

= 2.0 [12/18/2012] =
* NEW: Logging of the Optimizations (optional)
* NEW: Scheduling Optimizations for Automatic Execution (optional)
* Many other (technical and cosmetical) changes and improvements

= 1.3.4 [12/14/2012] =
* Changed the buttons for WP 3.5

= 1.3.3 [12/01/2012] =
* Some layout changes

= 1.3.2 [11/14/2012] =
* Shows more information about the optimized tables + other minor changes

= 1.3.1 [10/07/2012] =
* Minor changes

= 1.3 [10/06/2012] =
* Extra button for starting optimization, shows savings (in bytes) now

= 1.2 [10/03/2012] = 
* Major update: new options 'delete trash', 'delete spam', 'only optimize WordPress tables'

= 1.1.9 [09/27/2012] =
* Using a different method for retrieving database table names

= 1.1.8 [09/08/2012] =
* Another link fix

= 1.1.7 [09/03/2012] =
* Some textual and link fixes

= 1.1.6 [09/01/2012] =
* Fixed the link to the settings page

= 1.1.3 [09/01/2012] =
* Moved the 'Optimize DB Settings' item to Dashboard 'Settings' Menu and the 'Optimize Database' item to the Dashboard 'Tools' Menu. That makes more sense!

= 1.1.2 [08/30/2012] =
* Minor bug fix for the new option page

= 1.1 [08/29/2012] =
* Added: a new option page, in de plugins section, where you can define the maximum number of - most recent - revisions you want to keep per post or page

= 1.0.5 [08/21/2012] =
* Depreciated item ('has_cap') replaced, abandoned line of code removed

= 1.0.4 [06/06/2012] =
* Now also works with non short_open_tag's

= 1.0.3 [12/15/2011] =
* Some minor layout updates

= 1.0.2 [12/02/2011] =
* Some minor updates

= 1.0.1 [11/24/2011] =
* A few updates for the readme.txt file

= 1.0 [11/22/2011] =
* Initial release

== Frequently Asked Questions ==

= How can I change the settings of this plugin? =
* Change the settings in the WP Admin Panel &raquo; Settings &raquo; Optimize Database -or- via the WP Admin Panel &raquo; Optimize Database icon (depends on settings)

= How do I run this plugin? =
* WP Admin Panel &raquo; Optimize Database. Then click the 'Start Optimization'-button -or- via the WP Admin Panel &raquo; Optimize Database icon (depends on settings)
* Click the 'Optimize DB (1 click)' link in the Admin Bar (if enabled)

= Why do I see 'InnoDB table: skipped...'? =
* That's because optimizing InnoDB tables is not really efficient, so change the table type to MyISAM to have them being optimized.
* Update: if you want to optimize your InnoDB tables too, just check the 'Optimize InnoDB tables too' option on the settings page

= I scheduled the optimization for 8pm but it runs at 6pm (my local time) =
* The scheduler uses the local time of the web server which can differ from your own local time
