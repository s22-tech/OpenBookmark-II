https://github.com/s22-tech/openbookmark


See http://www.frech.ch/openbookmark/installation.php for installation instructions.

If you are upgrading from an old version, please manually create the following column on the `user` table:
	theme varchar(50) NOT NULL default ''



------



New Installation:

Basically the installation of OpenBookmark is not a big deal. There is an install script that guides you through the install steps.  Just make sure you know your servers database credentials.

    Download: First of all, download the latest Version of OpenBookmark and unpack the tarball in your Webserver's DocumentRoot direcotry.
    Installation: Now point your browser to the freshly created directory on your webserver. If you did it well, the URL might look like this:

    http://www.yourserver.com/openbookmark/

    If the configuration file "./config/config.php" is missing - and it most probably will be if you are installing a new version - then a page will be shown linking to the install script.  After answering all the questions in the install script, a config section is being displayed.  Copy the section and paste it to "./config/config.php".  The section looks like this.  After creating the config file you must delete the install.php file prior visiting OpenBookmark, just for security reasons.

    Login: Try visiting http://www.yourserver.com/openbookmark/ and login using the user that the install script created for you.  The username and password is "admin".  The first thing you should do is changing the password.  Then open the admin script and start creating other users.  You can find the admin script under the menu option "Tools".


Upgrade an existing Installation:

If you have an already existing installation of OpenBookmark and you would like to upgrade, the install script will care about the needed steps. If you are wise, you will backup the working installation before using the new one:

    Backup files: I simply rename or move the old installation of OpenBookmark so that the new version can take place:

    # mv /path/to/openbookmark \
         /path/to/openbookmark-backup

    Backup the database: Then I backup the database content by feeding this command to the MySQL server:

    # mysqldump --user=bookmarkmgr                 \
                --password=password_of_bookmarkmgr \
                bookmarks                          \
                > /some/path/openbookmark-backup.sql

    Upgrade: Now proceed with the steps from the description above. Don't forget to copy the favicons to the new directory:

    # cp -a /path/to/openbookmark-backup/icons/* \
            /path/to/openbookmark/icons/

    Admin user: If you upgraded and using at least version 0.8.7_beta now, the new admin script will be available. However, it will only be accessible by admin users. Thus you have to define one ore more users as admins now. To do so login to your MySQL server and do the following change in your database:

    # mysql -p bookmarks
    Enter password:
    ...
    mysql> UPDATE user SET admin='1' WHERE username='some_user';


Install favicon support:

	Since version 0.6.4 there is a nice feature with OpenBookmark that allows you to display the favicon of a webpage if one exists.  To make this work, do the following steps:

    Make sure ImageMagick is installed on your server. This is used to convert the favicon to PNG, since some browsers cannot display ICO files.

    Edit the "./config/config.php" file and set the variables $convert and $identify to where the "convert" and "identify" executables are on your system. It might be something like "/usr/local/bin/convert" or "/usr/bin/convert".

    Make sure that the "./favicons" directory is writable, readable and executable by the user your webserver runs as. Just chmod it to 755 and it will be.
