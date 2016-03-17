# Papi Changelog

## [3.0.3](https://github.com/wp-papi/papi/releases/tag/v3.0.3) - 2016-XX-XX

* Fixed: Box options didn't work when no properties exists in the box.
* Fixed: Some properties that was stored in options table did return null from `papi_get_option`, mostly flexible and repeater.

## [3.0.2](https://github.com/wp-papi/papi/releases/tag/v3.0.2) - 2016-03-08

### Fixed

* Fixed: PHP Notice/Object issue for relationship when using custom relationship data.
* Fixed: Double fields issue for any field when using `papi_get_field` in hooks that fires earlier then `admin_init` [#153](https://github.com/wp-papi/papi/issues/153).

## [3.0.1](https://github.com/wp-papi/papi/releases/tag/v3.0.1) - 2016-02-15

### Fixed

* Fixed: Render issue with tabs that existed in template files [#148](https://github.com/wp-papi/papi/issues/148).
* Fixed: Edit link property did appear in the default editor.
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

## [2.4.7](https://github.com/wp-papi/papi/releases/tag/v2.4.7) - 2015-11-12

Fixed: Template did return empty string when no template was found.
Fixed: Select2 overflow, see [issue #105](https://github.com/wp-papi/papi/issues/105).

## [2.4.6](https://github.com/wp-papi/papi/releases/tag/v2.4.6) - 2015-11-11

### Added

Added: Support for loading template files from a child theme if it exists.

### Fixed

Fixed: Empty value didn't show as it should when flexible had more then 9 fields.

## [2.4.5](https://github.com/wp-papi/papi/releases/tag/v2.4.5) - 2015-11-09

### Fixed

Fixed: Option will save on `admin_init` now since link property use `url_to_postid` and calling it to early didn't work.
Fixed: Output for link property when saved in option table.

## [2.4.4](https://github.com/wp-papi/papi/releases/tag/v2.4.4) - 2015-11-04

### Added

Added: `_self` as default target value for link property.

### Fixed

Fixed: Link property will deleted values when removing a link.
Fixed: Flexible empty values did not work because wrong regex.

## [2.4.3](https://github.com/wp-papi/papi/releases/tag/v2.4.3) - 2015-10-30

Fixed: Relationships children did not showed correctly on page load.
Fixed: Action `papi/include` caused recursive loop.

## [2.4.2](https://github.com/wp-papi/papi/releases/tag/v2.4.2) - 2015-10-27

### Added

* Added: `post_type` option to properties. Useful when a page type is used for more then one post type but not all properties.
* Added: `add_new_label` setting to flexible and repeater properties. See issue [#106](https://github.com/wp-papi/papi/issues/106).

### Fixed

* Fixed: `disabled` works in properties that are used in flexible and repeater. Disabled properties won't be rendered in flexible and repeater.
* Fixed: `display` works in properties that are used in flexible and repeater. Table rows will be hidden when properties has `display => false`. You can show them but removing `.papi-hide` css class when you like to display them.
* Fixed: bool property can handle `false` as empty. See pull request [#103](https://github.com/wp-papi/papi/pull/103), thanks [@rasmusbe](https://github.com/rasmusbe)!
* Fixed: `Add new page` view is only added when page types exists.
* Fixed: Property default value is only used when post is not created or post status is `auto-draft`

## [2.4.1](https://github.com/wp-papi/papi/releases/tag/v2.4.1) - 2015-10-15

* Fixed: `papi_is_option_page` issue where it didn't check `page` query string right.

## [2.4.0](https://github.com/wp-papi/papi/releases/tag/v2.4.0) - 2015-10-15

### Added

* Added: `items` setting to relationship so you can add your own data source!
* Added: Image urls to image sizes array in file/image/gallery property.
* Added: `Papi_Attachment_Type`
* Added: `papi-after-html` and `papi-before-class`
* Added: Term property, thanks [@rasmusbe](https://github.com/rasmusbe)!
* Added: Default property value option. See issue [#95](https://github.com/wp-papi/papi/issues/95).
* Added: `max`, `min` and `step` in number property. See issue [#91](https://github.com/wp-papi/papi/issues/91).
* Added: `after_class` and `before_class` to property option. So you can add your own css class to after and before div.
* Added: `display` option to properties. Flexible and repeater will hide the property and not the row.
* Added: `post_id` to link property output object if it's a internal link.

### Changed

* Changed: Standard type is hidden by default instead of showed. This will effect all standard type filters.

### Fixed

* Fixed: Link property issue when slug was same as a property name. See issue [#99](https://github.com/wp-papi/papi/issues/99).
* Fixed: Add new link issue when you have a post type on second menu level. See issue [#94](https://github.com/wp-papi/papi/issues/94).
* Fixed: Relationship property issue in flexible or repeater. See issue [#93](https://github.com/wp-papi/papi/issues/93).
* Fixed: Embed issue when a embed link didn't embed as it should in custom editor.

## [2.3.5](https://github.com/wp-papi/papi/releases/edit/v2.3.5) - 2015-10-07

* Fixed: Cache issue where Papi din't delete cache on save. See issue [#98](https://github.com/wp-papi/papi/issues/98).
* Fixed: Editor property issue where `the_content` filter was run on saved.
* Fixed: `allow_html` on `string` and `text` properties. See issue [#79](https://github.com/wp-papi/papi/issues/70).

## [2.3.4](https://github.com/wp-papi/papi/releases/tag/v2.3.4) - 2015-09-18

* Fixed: Get field issue when query string is used.
* Fixed: Relationship right list so it only contains objects.

## [2.3.3](https://github.com/wp-papi/papi/releases/tag/v2.3.3) - 2015-09-18

* Fixed: Issue with Papi ajax when multisite use subdirectories See issue [#90](https://github.com/wp-papi/papi/issues/90).

## [2.3.2](https://github.com/wp-papi/papi/releases/tag/v2.3.2) - 2015-09-09

* Fixed: Column issue with custom post types.

## [2.3.1](https://github.com/wp-papi/papi/releases/tag/v2.3.1) - 2015-09-08

* Fixed: `papi_html_tag` issue when the text is a callable function.

## [2.3.0](https://github.com/wp-papi/papi/releases/tag/v2.3.0) - 2015-09-08

### Added

* Added: `before_html` and `after_html` options that you can use to output html before property html and after property html.
* Added: `child_types` (old `page_types`).
* Added: `standard_type` for show/hide standard page type when having a page type in a parent post.
* Added: New filter for show/hide standard page type from filter dropdown. `papi/settings/show_standard_page_type_in_filter_{$post_type}`
* Added: New property for adding links with the link popup that exists in the editor.
* Added: Support for dot templates in page type, so instead of `pages/article.php` can you write `pages.article.php` or `pages.article` without `.php` extension. The old way will continue to work.
* Added: Import and export layer that can be used when importing data to Papi or exporting.
* Added: `papi_get_page_type_key` function that will return the meta key that Papi use to save page type id in `postmeta` table.
* Added: `papi_set_page_type_id` function so you can set page type id to a post.
* Added: `papi_page_type_exists` function so you can check if a page type file exists.
* Added: `papi_option_type_exists` function so you can check if a option type file exists.

### Changed

* Changed `Add new page type` to `Add New Page` (`Page` is the post type name).
* Changed filter title from `Show all page types` to `All types`
* Changed column title from `Page Type` to `Type`

### Fixed

* Fixed: `wp-admin` is not hardcoded on the management page.
* Fixed: `2%F` is replaced with `/` when setting the current menu item.
* Fixed: `post_type` query for post post type works as it should.
* Fixed: All `h2` tags are `h1`, see [Headings in Admin screens change in WordPress 4.3](https://make.wordpress.org/core/2015/07/31/headings-in-admin-screens-change-in-wordpress-4-3/) for more info.

## [2.2.2](https://github.com/wp-papi/papi/releases/tag/v2.2.2) - 2015-09-03

* Fixed: `papi-ajax` returns 200 status code always. When using custom permalink structure it returned 404.

## [2.2.1](https://github.com/wp-papi/papi/releases/tag/v2.2.1) - 2015-08-12

* Fixed: Allow html bug. See issue [#79](https://github.com/wp-papi/papi/issues/79).

## [2.2.0](https://github.com/wp-papi/papi/releases/tag/v2.2.0) - 2015-08-03

### Added

* Added: Conditional logic for properties.
* Added: Property file. See [issue #71](#71).
* Added: Property user.
* Added: Select2 setting to Dropdown, Post and User.

### Fixed

* Fixed: Labels for attribute.
* Fixed: Format_value and update_value for flexible and repeater
* Fixed: Array to string [issue #75](#75).
* Fixed: Translation [issue #73](#73).
* Fixed: Admin column [issue #72](#72).
* Fixed: `h2` is `h1`. See [Headings in Admin screens change in WordPress 4.3](https://make.wordpress.org/core/2015/07/31/headings-in-admin-screens-change-in-wordpress-4-3/)

### Thanks

Thanks to [@nlemoine](https://github.com/wp-papi/papi/issues?utf8=%E2%9C%93&q=author%3Anlemoine+) for finding some bugs and feature request.

## [2.1.1](https://github.com/wp-papi/papi/releases/tag/v2.1.1) - 2015-07-25

* Fixed: Image property will render SVG on page load correctly.

## [2.1.0](https://github.com/wp-papi/papi/releases/tag/v2.1.0) - 2015-06-24

### Added

* Added: French translation by [@dflorent](https://github.com/dflorent)

### Changed

* Changed: Tool page titles

### Fixed

* Fixed: SVG didn't work with image property. See issue [#68](https://github.com/wp-papi/papi/issues/68).
* Fixed: Option type will not be displayed under the tool page since they don't have post id

## [2.0.0](https://github.com/wp-papi/papi/releases/tag/v2.0.0) - 2015-06-22

Papi 2.0.0 is a big release with a flexible repeater, updated properties, option type (page type for options page), new API functions and bug fixes. Some internal functions has been removed or renamed.

Upgrading from Papi 1.x to 2.x will not be a big step and there is a upgrade guide describing what is changed since 2.x.

### Requirements

* WordPress >= 4.0
* PHP >= 5.4.7

### Enhancements

* Added: `display( $post_type )` method to page type class, works like `papi/settings/show_page_type_{$post_type}`.
* New property: Flexible repeater, a repeater where you can have different layouts on each row.
* Option type, just like page type but for option pages.

#### Actions

* `papi/delete_value/{$property_type}` is called when you use `papi_delete_field` or `papi_delete_option`, the post id will be zero when deleting a option value.

#### Filters

* `papi/settings/standard_page_type_{$post_type}` is renamed to `papi/settings/show_standard_page_type_{$post_type}`

#### New functions

* `papi_get_slugs`, replacing `papi_get_fields`

#### Field

* `papi_get_field`, get the property database value. Replacing `papi_field`
* `papi_update_field`, update a property database value.
* `papi_delete_field`, delete the property database value.

#### Option

* `papi_delete_option`, delete property database value.
* `papi_get_option`, get property value from Papi option type.
* `papi_update_option`, update property database value.

#### Page type

* `papi_get_page_type_id`, get the page type id.
* `papi_get_page_type_name`, get the page type name.
* `the_papi_page_type_name`, echo the page type name.

### Properties

#### Datetime
* Upgrade moment.js to 2.10.3
* Upgrade Pikaday

#### Dropdown and Post

* `placeholder` setting is replacing `blank_text` and `include_blank`
* Upgraded Select2 to 4.0

#### Relationship

* `choose_max` is renamed to `limit`.

#### Repeater

* Added `limit` setting.
* Added `layout` setting with `table` or `row`. The `table` value will render the repeater as a table and the `row` value will render the repeater as a row on the height.

#### Deprecated

* `current_page` is deprecated since 2.0.0. Use `papi_get_page` instead.
* `papi_field` is deprecated since 2.0.0 and replaced with `papi_get_field`
* `papi_fields` is deprecated since 2.0.0 and replaced with `papi_get_slugs`
* `papi_get_page_type_meta_value` is deprecated since 2.0.0 and replacede with `papi_get_page_type_id`

### Thanks

Thanks to all contributors and all who have tested Papi during the development.
