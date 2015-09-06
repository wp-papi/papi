// Papi core
import Core from 'papi/core.js';
import Required from 'papi/required.js';
import Rules from 'papi/rules.js';
import Tabs from 'papi/tabs.js';

// Properties
import Color from 'papi/properties/color.js';
import Datetime from 'papi/properties/datetime.js';
import Dropdown from 'papi/properties/dropdown.js';
import Editor from 'papi/properties/editor.js';
import File from 'papi/properties/File.js';
import Flexible from 'papi/properties/flexible.js';
import Link from 'papi/properties/link.js';
import Post from 'papi/properties/post.js';
import Reference from 'papi/properties/post.js';
import Relationship from 'papi/properties/relationship.js';
import Repeater from 'papi/properties/repeater.js';
import Url from 'papi/properties/url.js';

/**
 * Initialize all imported classes.
 */

export function init() {
  // Papi core
  Core.init();
  Required.init();
  Rules.init();
  Tabs.init();

  // Properties
  Color.init();
  Datetime.init();
  Dropdown.init();
  Editor.init();
  File.init();
  Flexible.init();
  Link.init();
  Post.init();
  Reference.init();
  Relationship.init();
  Repeater.init();
  Url.init();
}
