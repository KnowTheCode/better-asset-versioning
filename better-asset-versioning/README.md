# Better Asset Versioning

This repository is for the [Better Asset Versioning Lab](https://knowthecode.io/labs/better-asset-versioning) on [Know the Code](https://KnowTheCode.io).

This WordPress plugin handles the following tasks:
 
- Asset URL conversion 
    - removing the asset version number query parameter from the URL
    - moving it into the filename
- Set theme's version number to its stylesheet's last modification time
- Change the theme's stylesheet to the minified version when not in debug mode. [See the code here](https://github.com/KnowTheCode/better-asset-versioning/blob/master/src/Support/asset-helpers.php#L14).    
 
## Asset URL Conversion - How it Works

If the configuration has it turned on, then the plugin loads the `URLConverter`.

### Step 1 - Validating the Registered Version Number
The first step is to validate the registered version number.  Why? We want to give each asset the ability to bypass the converter. 

This check looks up the registered version number, i.e. when the asset was enqueued, and then checks if it's set to `null`. If yes, then the version number query var is stripped off and then URL is returned.  No conversion happens. 

When would we want to bypass the conversion? If the asset URL has the version number as part of the `path/to`, there's no need to convert it. An example is Font Awesome.

### Step 2 - External Asset Checker

The next step is to check if the asset is external to the website, such as Google Fonts, OptinMonster, and others.  We do not want to touch those URLs as they are handled externally on servers that we do not control.
 
### Step 3 - Conversion

Finally, if we get to this point, the conversion will occur.  The `URLConverter` strips the query var and moves it into the filename between the filename and extension.

For example, here is a theme's CSS URL:

`http://domain.dev/wp-content/themes/your-theme/style.css?ver=2.2.4`

Notice that a query key/value pair are appended to the end of the URL.  Our goal is to remove the URL query parameter and then embed it into the static asset's URL.

This plugin converts the above example into:

`http://domain.dev/wp-content/themes/your-theme/style.2.2.4.css`

Notice that the version number is now part of the filename.

NOTE:  This technique is copied from other cache-busting techniques, such as [`fbcfwss.php` from ocean90](https://gist.github.com/ocean90/1966227) and HTML5 Boilerplate.

## Rewrites

We've changed the asset's URL.  The filename is different than the actual file on the web server.  Therefore, we need to add a rewrite rule to let the web server handle stripping out the version number in order to locate and serve up the actual file.

For example:

`http://domain.dev/wp-content/themes/your-theme/style.2.2.4.css`

would be changed internally to:

`http://domain.dev/wp-content/themes/your-theme/style.css`

Why? There is no file in the theme called `style.2.2.4.css`. Right? This plugin added the version number `2.2.4` between the filename and extension.  In order for the web server to locate that file, we need to modify the URL back to what it should be.

### Apache Server

For an Apache server, you need to open up the `.htaccess` file and add the following to the top of it:

```
   # START - REWRITE ASSET VERSIONS
   <IfModule mod_rewrite.c>
   RewriteEngine On
   RewriteBase /
   
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.+)\.(.+)\.(min.js|min.css|js|css)($|\?.*$) $1.$3 [L]
   </IfModule>
   # END - REWRITE ASSET VERSIONS
```

Notice that we are adding a `RewriteRule` to identify the filename pattern and then rewrite it to the actual file.

CREDIT: This configuration comes from [ocean90](https://gist.github.com/ocean90/1966227).


### Nginx Server (including VVV)

For an Nginx server, you need to open up the website's `.conf` file and add the following to the top of it:

```
    # START - REWRITE ASSET VERSIONS
    location ~* (.+)\.(?:\d+)\.(min.js|min.css|js|css|png|jpg|jpeg|gif)$ {
      try_files $uri $1.$2;
    }
    # END - REWRITE ASSET VERSIONS
```

CREDIT: This configuration comes from [HTML5 Boilerplate](https://github.com/h5bp/server-configs-nginx/blob/master/h5bp/location/cache-busting.conf).

## Setting Your Asset's Version Number

Within the [asset-helpers](https://github.com/KnowTheCode/better-asset-versioning/blob/master/src/Support/asset-helpers.php#L38) file, there's a function that lets you set your asset's version number to the last time it was modified.  No more hardcoding in a version number. Nope, let PHP grab the file's last modification time for you.
 
When enqueueing, do this:  

```
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );
/**
 * Enqueue theme assets.
 *
 * @since 1.0.0
 */
function enqueue_assets() {
	$asset_file = '/assets/dist/js/jquery.plugin.min.js';
	
	wp_enqueue_script(
		'plugin_js_handle',
		CHILD_URL . $asset_file,
		array( 'jquery' ),
		get_file_current_version_number( CHILD_THEME_DIR . $asset_file ),
		true
	);
}
```

Notice that the version number is using this plugin's `get_file_current_version_number()` function to grab the asset file's last modification time. 

When enqueuing an asset that already has the version number as part of the `path/to`, do the following:

```
	wp_enqueue_style(
		'plugin_js_handle',
		'//maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css',
		array(),
		null
	);
```

## Installation

Installation from GitHub is as simple as cloning the repo onto your local machine.  To clone the repo, do the following:

1. Open up terminal and navigate to your website's folder and into `wp-content/plugins/`.
- If you have PhpStorm, use the "Terminal" option that's built right into the IDE.
- Otherwise, if you are on a Mac, then use Terminal.  For Windows, use Git Bash.
2. Then type: `git clone https://github.com/KnowTheCode/better-asset-versioning.git`.
3. Activate the 'Better Asset Versioning' plugin.
4. Open up your `.htaccess` file for Apache or the `domain.conf` file on Nginx for the website you are working on.
5. Copy the above code and paste it into the file.
    - For Apache, it goes on line 1.
    - For VVV, I put it on line 1 in the `vvv/config/nginx-config/nginx-wp-common.conf` file. 
6. Save it.
6. Close it.

That's it.  You're ready to do the lab with me.

## Contributions

All feedback, bug reports, and pull requests are welcome.