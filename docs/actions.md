# Actions

### act/include_property_types

Include third party properties.

Example:

```
<?php

  function include_property_kvack () {
    include_once('class-property-kvack.php');
  }
  
  add_action('act/include_property_types', 'include_property_kvack');

?>
```