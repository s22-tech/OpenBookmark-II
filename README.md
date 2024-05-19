# OpenBookmark-II
OBM II is a rewrite of Brendan LaMarche's fantastic OpenBookmark script, to bring it back to life and run on modern versions of PHP.  It retains the same 3-column layout, but has some major changes under the hood.  One of them is that favicons are now named with the domain name so that only a single favicon per domain is saved to your drive.  This will help keep your inode count low if you save a lot of links.  Besides, why save multiple icons when one will do?

## To Do
The look of this script hasn't changed from the original, since CSS is not my strong suit.  If someone would like to help update the look and feel of this script, please send a pull request.

## Installation
To install, simply copy the files to your server and adjust the values in `/config/config.php` to match your database and server.

**N.B.**  This version must be installed at the top level of your domain or a sub-domain.  It will not work if installed in a sub-directory.  I'm working on a fix.

### Disclaimer
This script was uploaded to share it with others who miss the functionality of the original OpenBookmarks.  Unfortunately, I don't have the time to make it into a one-size-fits-all solution.  If you have ideas of new features to add, feel free to let me know.  However, if I don't feel they'd be a good fit, I probably won't add it myself.  But pull requests are always welcome.  The more people helping with this script, the better.
