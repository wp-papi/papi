# Papi Changelog

## [3.1.0](https://github.com/wp-papi/papi/releases/tag/v3.1.0) - 2016-XX-XX

### Added

* Added: `show_screen_options` to all types meta method that can turn off screen options tab.
* Added: `show_help_tabs` to all types meta method that can turn off help tab.
* Added: `help` method to all types which can be used to add help tabs.
* Added: `help_sidebar` method to all types which can be used to add help tabs sidebar content.
* Added: Support for taxonomy types with support for term meta.
* Added: `wp papi term` command like post command.
* Added: `mce_buttons` settings to editor property.
* Added: `media_buttons`, `teeny` and `drag_drop_upload` settings to editor property.
* Added: Vertical boxes support [#148](https://github.com/wp-papi/papi/issues/159).
* Added: Support for saving properties on revision posts and restoring revision data.
* Added: `display` option to box options in order to control if the box should be displayed or not.
* Added: Support for repeaters inside repeaters.
* Added: Support for repeaters inside flexibles.
* Added: Support for render html in `Publish box` with `publish_box` method.
* Added: `papi_get_entry_type_css_class` to get the css class that is added to body for a entry type.
* Added: Support for autosaving fields.
* Added: Support for group inside flexibles.
* Added: Support for group inside repeaters.
* Added: `body_classes` method to all types which can be used to add custom body classes.
* Added: `show_permalink` to page type so permalink div can be hidden.

### Changed

* Updated: Cross icon for file property with new color [#158](https://github.com/wp-papi/papi/pull/158).
* Updated: `$id` param for `papi_get_slugs` is optional.
* Updated: Group property is stored as a repeater but only with one row and not a standalone properties.

### Removed

* Removed: Papi tool page.

## [3.0.7](https://github.com/wp-papi/papi/releases/tag/v3.0.7) - 2016-05-09

* Fixed: Locale should be restored after `papi_slugify` is used [#169](https://github.com/wp-papi/papi/issues/169).
* Fixed: Show standard page type filter is used when only one page type exists.

## [3.0.6](https://github.com/wp-papi/papi/releases/tag/v3.0.6) - 2016-04-14

* Fixed: Attachment types used on a page type didn't load right, so the site performance was bad.
* Fixed: Object cache issue with properties that overwrites a existing post field. Cached data was loaded in WordPress admin.

## [3.0.5](https://github.com/wp-papi/papi/releases/tag/v3.0.5) - 2016-04-01

* Fixed: Datetime did not work in repeater [#166](https://github.com/wp-papi/papi/issues/166).

## [3.0.4](https://github.com/wp-papi/papi/releases/tag/v3.0.4) - 2016-03-23

* Fixed: Require for link property [#165](https://github.com/wp-papi/papi/issues/165).
* Fixed: Check so file exists before calling `file_get_contents` when reading page type files.

## [3.0.3](https://github.com/wp-papi/papi/releases/tag/v3.0.3) - 2016-03-19

* Fixed: Box options didn't work when no properties exists in the box.
* Fixed: Some properties that was stored in options table did return null from `papi_get_option`, mostly flexible and repeater.
* Fixed: Object cache issue with options fields that did get post id instead of zero that options should have.
* Fixed: Check for registered directories before they are used.

## [3.0.2](https://github.com/wp-papi/papi/releases/tag/v3.0.2) - 2016-03-08

### Fixed

* Fixed: PHP Notice/Object issue for relationship when using custom relationship data.
* Fixed: Double fields issue for any field when using `papi_get_field` in hooks that fires earlier then `admin_init` [#153](https://github.com/wp-papi/papi/issues/153).

## [3.0.1](https://github.com/wp-papi/papi/releases/tag/v3.0.1) - 2016-02-15

### Fixed

* Fixed: Render issue with tabs that existed in template files [#148](https://github.com/wp-papi/papi/issues/148).
* Fixed: Edit link property did appear in the default editor.
* Fixed: Required did not output the red wildcard [#149](https://github.com/wp-papi/papi/issues/149).
* Fixed: Required did not output the red wildcard [#149](https://github.com/wp-papi/papi/issues/149).

## [3.0.0](https://github.com/wp-papi/papi/releases/tag/v3.0.0) - 2016-02-02

Papi 3.0.0 is a big release since a big piece of the core code has been refactored to improve how page type works. With 3.0.0 release we introduce `Entry Type` which is a base class that both page type and option type use. Both `box` and `tab` logic has been rewritten with new core classes and the admin classes has been divided into several smaller classes. Some internal functions has been removed or rewritten with backward compatibility.

We moved some logic from page type class to entry type class to be able the separate page type and option type class. This will make it easier to add new types to core or create plugin that has custom types.

### Added

* Added: Extended support for `meta`, `box` and `remove` methods [#114](https://github.com/wp-papi/papi/issues/114).
* Added: WP CLI Support [#111](https://github.com/wp-papi/papi/issues/111).
* Added: Group property [#112](https://github.com/wp-papi/papi/issues/112).
* Added: `papi/before_init` action that is fired before Papi loads textdomain, classes, functions and setups the container.
* Added: `papi/init` action that is fired after Papi loads textdomain, classes, functions and setups the container.
* Added: `papi/loaded` action that will be the new `papi/include`. The old action is deprecated but will still work, it's fired before `papi/loaded` and will be removed in a feature version of Papi.
* Added: `placeholder` setting to string property.
* Added: Description to option type meta data.
* Added: `papi/settings/column_hide_{$post_type}` for hiding type column.
* Added: `papi/template_include` to provide support for third party templating engines.
* Added: Layout mode to post property. It can devide into multiple select or a single select with labels (as before).
* Added: Layout mode to term property. It can devide into multiple select or a single select with labels (as before).
* Added: Second bool param to `papi_get_slugs` that will return only slugs if true.
* Added: A way to handle classes with the same name in multiple directories [#107](https://github.com/wp-papi/papi/issues/107)
* Added: When WordPress refresh nonces, Papi nonces should be refreshed too.

### Changed

* Flexible property will only save one layout per row. The layout key is changed from `_layout` to `_flexible_layout` since `_layout` can be a real slug. It has backward compatibility for the old layout key. This may cause problem with existing slugs that are named `layout`. To fix default value issue you need to manually add the layout value for the effected row. The slug will be something like this: `sections_0_flexible_layout`, where `sections` is your flexible slug, `0` is the row and `_flexible_layout` is the new layout key. The value should be a slug of the layout title, the same value as the old `_layout` rows that exists on each property.

### Fixed

* Fixed: Save post issue when using property template file and overwrited the slug [#129](https://github.com/wp-papi/papi/issues/129).
* Fixed: Select2 clear issue [#132](https://github.com/wp-papi/papi/issues/132).
* Fixed: Property type values like  `test-form-1` should match `Papi_Property_Test_Form_1`
* Fixed: When using `overwrite` it should read data from the post instead of post meta [#145](https://github.com/wp-papi/papi/issues/145)
* Fixed: Same prefix on folders should not replace all, only the current one.

### Removed

* Removed: `remove` method is removed.
* Removed: No more array properties, all properties must use `papi_property` or `$this->property` to work. This because the converting of array properties is bad and some keys can't be used for other things.
* Removed deprecated function `current_page`, was deprecated in 2.0.0.
* Removed deprecated function `papi_field`, was deprecated in 2.0.0.
* Removed deprecated function `papi_fields`, was deprecated in 2.0.0.
* Removed deprecated function `papi_get_page_type_meta_value`, was deprecated in 2.0.0.
* Removed deprecated meta methods, `page_type` and `option_type`, `meta` method should be used instead.

### Upgraded

* Upgraded: Moment.js from 2.10.6 to 2.11.0

### Thanks

Thanks to all contributors and all who have tested Papi during the development.

You can find the old changelog for `2.x` [here](https://github.com/wp-papi/papi/blob/2.x/CHANGELOG.md).
