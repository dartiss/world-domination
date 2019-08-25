=== World Domination ===
Contributors: dartiss
Tags: market, coverage, share, w3tech, penetration, crm, wordpress
Requires at least: 4.6
Tested up to: 5.2.2
Requires PHP: 5.3
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add WordPress market coverage summary to your dashboard.

== Description ==

This plugin adds a summary of the current WordPress market coverage to your dashboard!

Basically screen scraping from the [W3Techs](https://w3techs.com/technologies/details/cm-wordpress/all/all "W3Techs") website (don’t panic W3Techs, I’m caching the data - your website performance is safe!), this will display what percentage of websites (in total or that use CRM) are currently powered by WordPress. There are even shortcodes so that you can this information into your posts as well!

Now you can keep an eye on how close to world (aka internet) domination WordPress is achieving <cue diabolical laughter>.

https://www.youtube.com/watch?v=gY2k8_sSTsE

* Designed for both single and multi-site installations
* PHP7 compatible
* Passes [WordPress.com VIP](https://vip.wordpress.com) coding standards and fully compatible with their platform
* Fully internationalized, ready for translations **If you would like to add a translation to his plugin then please head to our [Translating WordPress](https://translate.wordpress.org/projects/wp-plugins/world-domination "Translating WordPress") page**
* Gutenberg ready

Please visit the [Github page](https://github.com/dartiss/world-domination "Github") for the latest code development, planned enhancements and known issues.

== Using the shortcodes =

There are two shortcodes `[wp_total_market]` and `[wp_crm_market]`. Simply add these, wherever you wish within a post or page, to display the latest total or CRM market share data. 

== Installation ==

World Domination can be found and installed via the Plugin menu within WordPress administration (Plugins -> Add New). Alternatively, it can be downloaded from WordPress.org and installed manually...

1. Upload the entire `world-domination` folder to your `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress administration.

Voila! It's ready to go.

== Frequently Asked Questions ==

= Hey! My dashboard figure is different to the one shown on W3Techs =

For performance reasons I cache the dashboard information for one week so, if the market figure changes in the meantime, you will get this discrepancy. If you find this keeps happening, please let me know and I’ll look at refreshing the cache more regularly.

== Screenshots ==

1. How it appears on the dashboard with Akismet enabled.

== Changelog ==

[Learn more about my version numbering methodology](https://artiss.blog/2016/09/wordpress-plugin-versioning/ "WordPress Plugin Versioning")

= 2.0.1 =
* Enhancement: Code quality enhancements to bring it in line with WordPress.com VIP coding standards
* Enhancement: Now uses `wp_remote_get` alternative when run on the WordPress.com VIP platform

= 2.0 =
* Enhancement: Total re-write of the caching to ensure that if the data is not available previous information will be re-used (less chance of an error message as a result but, on the down side, may see older data as a result)
* Enhancement: Sides shored up with lots of security additions - escape them all!
* Enhancement: Now I show the CRM percentage on the dashboard and not just the overall one
* Enhancement: Brand new shortcodes so that you can embed this information in a post if that's your bag. Baby.
* Enhancement: Using a time constant for the caching rather than hard-coding long strings of numbers that I'm only likely to type wrong
* Enhancement: The Github links are on me! Now added to all the files and all the meta
* Enhancement: When you hover over the dashboard link to the source it will now show you when the data was last updated
* Enhancement: Added a timeout to the file fetching. Okay, so it's the default (5 seconds) but it's now easier for me to tweak that, if needs be
* Bug: Some of the plugin meta was missing due to the wrong plugin name being used. Oops. Needless to say, it's fixed

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 2.0.1 =
* Now fully compatible with the WordPress.com VIP platform