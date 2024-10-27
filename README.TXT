=== Amazon Store Plugin For WordPress ===
Contributors: A. Lewis
Tags: store,shop,amazon
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: trunk

Amazon Store Plugin For WordPress allows to integrate an Amazon store into your WordPress blog

== Description ==

With your Amazon Associates and Web Services accounts you can import product data from Amazon using keywords and build WordPress posts with the imported data. Amazon Store Plugin For WordPress helps you maintain an integrated store on your WordPress blog that list products from Amazon which your users can add to a shopping cart on your site. There are plenty of other similar products including some provided by Amazon. Amazon Store Plugin For WordPress allows you to integrate your store content into your blog. You create a main store category with any number of sub categories under it. When you import products posts are auto-created in the store category your select.

For more information and support please visit http://wppluginsdev.com/


== Credits ==

Copyright 2010 A. Lewis 


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA



== Installation ==

Backup your WordPress database and files before you install or update. 

To use this plugin:

1. Install and activate the plugin

2. (Optional) Create a page (name it anything you like) and add [ASPFWPDISPLAYSTORE] as the page content (include the brackets). This will create a page that displays your imported store items.

3. Setup a parent category for your store/shop imports. If you need multiple categories set them up as needed under the main parent category.

4. Go to the dashboard under Amazon Shop and setup the necessary options

5. Create a folder inside your theme folder and name the folder "single"

6. Create a duplicate of your theme file single.php and name the duplicate single-cat-store.php

7. Open single-cat-store.php and place &lt;?php aspfwp_single_template_code();?&gt; below the loop. This code should replace the code that handles the post entry items.

8. Place single-cat-store.php into the folder "single"

9. Add &lt;?php aspfwp_view_cart_link();?&gt; where you would like to place the "View Cart" link. Recommended that you place it somewhere in header.php

10. Add &lt;?php aspfwp_configure_checkout_link();?&gt; where you would like to place the "Checkout" link where users have items in their cart or the "Cart Empty" link where cart is empty. Recommended that you place it somewhere in header.php

11. Add &lt;?php aspfwp_total_items_in_cart();?&gt; where you would like to place the total items in cart information

12. Go to dashboard-&gt;Amazon Shop-&gt;Import to start importing.

Extra: If you want to modify the stylesheet and don't want to lose your custom styles when you upgrade you can copy the file amastyle.css to your template folder and modify that copy instead of the one in the plugin css folder

This plugin is a work in progress. It is not guaranteed to work for everyone. If you attempt an import and receive a "Fatal error" that your MYSQL server has gone away, it does not mean your MYSQL server has literally gone away. It means the server timed out while trying to do the import. The only thing that has happened is that the import has failed even if it manages to import one or two before it times out. This can be due to any number of factors including there being too many rows of data to be imported. You can try your import again with some narrower keywords. If the server still times out you're advised to uninstall the plugin and try another product. If the plugin otherwise does not work for you there are plenty of other alternatives. 
If you are getting error message about session already started related specifically to this plugin, go to your Amazon Shop Options and set the value for "Disable Session Start?" to "yes". It is necessary to be able to use sessions in order to track the user's shopping cart items.
If you are having problems please visit http://www.wppluginsdev.com for help.


== Screenshots ==


== Changelog ==



