import Color from 'papi-properties/color';
import Datetime from 'papi-properties/datetime';
import Dropdown from 'papi-properties/dropdown';
import Image from 'papi-properties/image';
import Post from 'papi-properties/post';
import Reference from 'papi-properties/post';
import Relationship from 'papi-properties/relationship';
import Repeater from 'papi-properties/repeater';
import Url from 'papi-properties/url';

/**
 * Initialize all properties.
 */

export function init() {
  Color.init();
  Datetime.init();
  Dropdown.init();
  Image.init();
  Post.init();
  Reference.init();
  Relationship.init();
  Repeater.init();
  Url.init();
}
