=== WooCommerce - 1C - Data Exchange ===
Contributors: https://codecanyon.net/user/itgalaxycompany
Tags: 1c, woocommerce, woocommerce 1c, woocommerce data exchange

== Description ==

The main task of the plugin is to implement the ability to synchronize data between the 1C accounting system and the WooCommerce product catalog.

= Features =

* Create and update categories.
* Create and update product properties and their values.
* Create and update products (and variations, if records are kept on the characteristics), images, prices and stocks.
* Support full exchange or change only.
* Unloading orders.
* Possibility of automatic set of the image category (for the first product with the image).
* Support for the adoption of data in the archive.
* The ability to control the size of the part when transferring files from 1C.
* Support for sequential loading (files received from 1C are processed with control of runtime).
* Ability to select the type of prices (if there are several unloading).
* All settings on the site through the administrative panel.
* Image previews.

== Installation ==

1. Extract `woocommerce-synchronization-1c.zip` and upload it to your `WordPress` plugin directory
(usually /wp-content/plugins ), or upload the zip file directly from the WordPress plugins page.
Once completed, visit your plugins page.
2. Be sure `WooCommerce` Plugin is enabled.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. Go to the `WooCommerce` -> `1C Data Exchange`.
5. Specify login details for authorization 1c.
6. Save settings.
7. Make the exchange setting on the 1c side.

== Changelog ==

= 1.34.5=
Fixed: item name order processing.
Fixed: removal of pre-existing attributes of the product, but now missing.
Fixed: progress set variation attributes to parent product.
Chore: use monolog.
Fixed: reset lookup `total_sales` after exchange.
Feature: another option for working with stock <= 0 - "Do not hide, but do not give the opportunity to put in the basket".

= 1.33.1 =
Chore: more customization of the order formation xml process.
Feature: more supports scheme variants position product code.
Feature: ability to choose which value will be recorded in the sku - the code from the requisites or the sku value.
Feature: if there is shipping, then use the real name of the shipping method.

= 1.30.0 =
Feature: ability to not update the product attributes.
Feature: ability to not update the product images.
Fixed: compatibility with `Admin Menu Editor`.
Feature: support for simultaneous exchange with multiple sites when using multisite mode - own exchange directory for each site.

= 1.27.1 =
Fixed: reset lookup `onsale` after exchange.
Feature: ability to change the start date of the upload of orders on the settings page.
Feature: do not erase product links with categories that have been manually added.
Fixed: do not delete tag `featured`.
Feature: support for the strange position of information on characteristics in the nomenclature instead of trading offers.

= 1.24.3 =
Fixed: do not change the description when a short description recording is activated.
Fixed: check, maybe the product property has been manually deleted.
Chore: more logs.
Feature: support processing length, width and height.
Feature: support processing of variations without properties, only with characteristics.
Feature: ability to select the encoding for the response with orders.

= 1.21.1 =
Fixed: (cgi/fcgi) filling in empty variables user and password.
Feature: support for linking multiple categories to one product.
Feature: processing of duplicated products as one of the information on the item, when 1C incorrectly generates data in xml.
Fixed: possible use of image upload error like image.
Fixed: set price / stock for variation at first creation.
Feature: saving all `ЗначениеРеквизита` in the product metadata `_all_product_requisites`.

= 1.18.0 =
Feature: processing of warehouses, as well as additional storage of stocks with separation by warehouses in the metadata of the products, if transferred with separation.
Fixed: set order currency if one price type.
Fixed: order currency is now dependent on the currency of the price type.
Feature: ability to not update the product title.

= 1.16.3 =
Fixed: unlink the category from the media file, when deleting the media file (when manually deleting).
Chore: more hooks to interact with the exchange order process.
Chore: added a filter to search for an existing product category before creating a new one.
Feature: more support in stock xml schemes.
Feature: ability apply price types depending on the amount in the cart.

= 1.14.2 =
Fixed: disable only variation - not parent product.
Fixed: encoding and version when forming a response to a request for orders.
Feature: сlean up missing product variations (optional).

= 1.13.3 =
Fixed: remove product relation old (non exists) variation attributes.
Fixed: disable the variation if it is not in the stock so that the values are not displayed for selection.
Chore: more logs order process.
Feature: ability to skip group processing.
Feature: ability to skip product post content/excerpt processing.

= 1.12.2 =
Fixed: disable stock management if stock 0 and the rule does not hide products.
Fixed: post counters in the term list.
Feature: support for new tag for offer package.

= 1.11.1 =
Fixed: set default `order` meta for terms to sorting.
Feature: ability to display a list of prices on the product page.

= 1.10.0 =
Feature: ability to search for an existing product by SKU, before creating a new one.

= 1.9.2 =
Chore: more set stock value logs.
Chore: more hooks to interact with the exchange process.
Feature: support for set sale price.

= 1.8.3 =
Chore: clean product transients after exchange.
Fixed: possible problem when processing variable properties in several variants of schemes.
Fixed: image processing cache based on the hash, since different images may come with the same name.
Feature: processing offer options if the type is not a "Справочник".

= 1.7.1 =
Chore: removed upper and lower limit for values `file limit` and `time limit`.
Feature: optional - write the product description in short description.
Feature: optional - skip products without photo.
Feature: ability to run queries manually.

= 1.4.2 =
Fixed: support new protocol order exchange.
Fixed: processing options if the type is not a "Справочник".

= 1.4.0 =
Feature: the ability to use the full description from the "Description file for the site".

= 1.3.5 =
Fixed: set `manage stock` based on setting `WooCommerce`.
Chore: more optimization when handling variations.
Chore: more optimization when handling requisites.

= 1.3.4 =
Fixed: resolve product options for variations for several formats.
Fixed: logic when processing images using the new protocol.
Fixed: save settings.
Fixed: incorrect deletion of old images.

= 1.3.0 =
Feature: support for more than one image (all but the first fall into the gallery).
Feature: the ability to ignore control hash of products by hash from the contents.
Feature: the ability to completely disable the removal of xml files received during the exchange.

= 1.2.0 =
Feature: support new protocol and scheme 3.1.

= 1.1.0 =
Feature: more support in stock xml schemes.

= 1.0.2 =
Fixed: reindex `Relevanssi`.
Fixed: reset cache `wc product lookup`.

= 1.0.0 =
Initial public release.
