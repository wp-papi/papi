# Papi Changelog

## [2.5.0](https://github.com/wp-papi/papi/releases/tag/v2.5.0) - 2015-XX-XX

Papi 2.5.0 is kind of a big release since some of the core code has been refactored to improve how page type works. With 2.5.0 release we introduce `Content Type` which is a base class that both page type and option type use. Both `box` and `tab` logic has been rewritten with new core classes and the admin classes smaller.

We moved some logic from page type class to content type class to be able the separate page type and option type class. This will make it easier to add new types to core or create plugin that has own types. Some internal functions has been removed or rewritten how it works.

`papi_get_all_page_types` is rewritten and use `papi_get_all_content_types` functions that is a new version of the old `papi_get_all_page_types` function. Several `papi_get_page_type_*` function has changed and is using `papi_get_content_type_*` functions.

With this version we changed how `remove` method works, it's removed from the page type class and will not work inside `register` method anymore. This is because we removed one of the calls to `register` method so the boxes isn't loaded twice.

The old way:

```php
public function register() {
  $this->remove( ['editor'] );
}
```

The new way:

```php
public function remove() {
  return ['editor'];
}

public function register() {

}
```

You will need to implement a method call `remove` that returns a string or array of strings that will be used to remove post type supports or meta boxes.

With 2.5.0 you can now extend page types and add properties in the extended page type to the box that exists in the parent page type. Useful when you have a base page type that you extend for other page types. See [issue #114](https://github.com/wp-papi/papi/issues/114). The `remove` method will load values from the parent page also.

### Added

* Added: WP CLI Support. See [issue #111](https://github.com/wp-papi/papi/issues/111).
* Added: Multiple property. See [issue #112](https://github.com/wp-papi/papi/issues/112).

### Fixed

### Removed

* Removed: `remove` method is removed.

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
