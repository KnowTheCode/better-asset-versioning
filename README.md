# Better Asset Versioning

This repository is for the Better Asset Versioning Lab on [Know the Code](https://KnowTheCode.io).

It is one WordPress plugin that handles:
 
- removing the asset version number query parameter from the URL
- moving it into the URL as a folder location
 
## How it Works
 
First, it validates if the conversion process should occur.  

If no, nothing happens.  We want that check for external assets such as Google Fonts, Bootstrap, and others.

If yes, then it converts the URL.

For example, here is the Dashicons CSS URL:

`http://domain.dev/wp-includes/css/dashicons.css?ver=4.8`

Notice that a query key/value pair are appended to the end of the URL.  Our goal is to remove the URL query parameter and then embed it into the static asset's URL.

This plugin converts the above example into:

`http://domain.dev/wp-includes/css/betterassetversioning-4.8/dashicons.css`

Notice that we've added a fictitious folder called `betterassetversioning-{version number}/`.

## Rewrites

There are multiple ways to handle rewrites so that WordPress knows how to properly route the asset to the actual file on the web server's hard drive.  In this lab, you need to add the following to your `.htaccess` file, i.e. put it at the very top:

```# START - REWRITE ASSET VERSIONS
   <IfModule mod_rewrite.c>
   RewriteEngine On
   RewriteBase /
   
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.+)/betterassetversioning-(.+)/(.+)$ $1/$3 [L]
   </IfModule>
   # END - REWRITE ASSET VERSIONS
```
This rewrite will remove our fictitious `path/to` that we added with the plugin.  Then WordPress is able to find that specific file. 

## Installation

Installation from GitHub is as simple as cloning the repo onto your local machine.  To clone the repo, do the following:

1. Open up terminal and navigate to your website's folder and into `wp-content/plugins/`.
- If you have PhpStorm, use the "Terminal" option that's built right into the IDE.
- Otherwise, if you are on a Mac, then use Terminal.  For Windows, use Git Bash.
2. Then type: `git clone https://github.com/KnowTheCode/better-asset-versioning.git`.
3. Activate the 'Better Asset Versioning' plugin.
4. Open up your `.htaccess` file for the website you are working on.
5. At the top of the file on line 1, copy the above code and paste it into the file.
6. Save it.
6. Close it.

That's it.  You're ready to do the lab with me.

## Contributions

All feedback, bug reports, and pull requests are welcome.