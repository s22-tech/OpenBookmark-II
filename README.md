# OpenBookmark-II
OBM II is a rewrite of Brendan LaMarche's fantastic OpenBookmark script, to bring it back to life and run on modern versions of PHP.  It retains the same 3-column layout, but has some major changes under the hood.  One of them is that favicons are now named with the domain name so that only a single favicon per domain is saved to your drive.  This will help keep your inode count low if you save a lot of links.  Besides, why save multiple icons when one will do?

## Features
<ul>
	<li>Handles a wide array of favicon types &mdash; including SVG and base64!</li>
	<li>Saves a single favicon per domain/sub-domain to keep file counts at a minimum.</li>
</ul>

## To Do
<ul>
	<li>Update CSS</li>
	<li>Add mobile view</li>
</ul>
This script works great on the desktop but it's useless on a phone.  Since CSS is not my strongsuit, if anyone would like to contribute a mobile view, that would be fantastic.  If you decide to submit a pull request, please keep in mind that I'm trying to keep this script as lightweight as possible, so adding jQuery and Bootstrap bundles is out of the question.  Also, this script must remain self contained, so no links to 3rd-party scripts.

## Requirements
<ul>
	<li>PHP 8.0 or higher</li>
	<li>Imagick</li>
	<li>convert (linux cli tool)</li>
	<li>identify (linux cli tool)</li>
	<li>php-curl extension</li>
</ul>

## Screenshots
![Main Screen](/images/screenshots/obm-main.png?raw=true "OBM II")

## Installation
To install, simply copy the files to your server and adjust the values in `/config/config.php` to match your database and server.

