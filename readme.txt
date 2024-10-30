=== Inquirer ===
Contributors: seedsca
Donate link: https://www.ecotechie.io/plans/
Tags: queries, performance, tests, accessibility, audit
Requires at least: 4.5
Tested up to: 5.8.2
Requires PHP: 5.6
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Run queries of an arbitrary URL on websites that can take these as query parameters.

== Description ==

When running a an audit on a website (yours, a competiror, or client), you can often find yourself going to around ten or more websites to run tests (Inquiries).
At each of these you then paste the same URL and run it's test and repeat for each one. Most of these services use the GET HTTP request method. You see these as the **?q=** part in [https://duckduckgo.com/?q=ecotechie](https://duckduckgo.com/?q=ecotechie).

I decided to write this plugin so I could input a URL once and run all of these tests more easily. No need to hunt down the website addressses or even leave the WordPress admin dashboard!

Features:

* Use any valid URL for testing, it doesn't have to be the current site the plugin is installed on.
* Add or remove websites to use for tests (Inquirers).
* Sortable by filter types like SEO, Speed, Security, etc. Or create your own tags...

== Screenshots ==

1. Add a URL to this input field and it will be used for the queries. There is no need to actually use the "Update URL for Query" button, if it isn't used it just won't be saved to the database.
2. Default Inquirers and their types. Options for deleting, using and filtering exist.
3. One filter being used, you can reset by using the "Reset Filter" button or pressing the selected buttons to deselect...
4. Add your own Inquirers, you can create your own filter types as well. The "Testing URL" is where the magic happens.

== Frequently Asked Questions ==

= Can I add any sites that do searches?=

If after running a test on a site, you see your url in the address bar, then yes.

= I have a great idea for improving this plugin, what do I do? =

You can reach me at [https://www.ecotechie.io/contact](https://www.ecotechie.io/contact). Fill out the contact for there and I'll see what I can do :)

== Changelog ==

= 0.1.0 =
* This is the initial release.

== Upgrade Notice ==

= 0.1.0 =
* No upgrades yet ;)
