# Filters

### act/update_value

Update value for every property before it is saved to the database.

Example:

```
<?php

  function eg_update_value ($value, $post_id) {
    // do some magic with the value and return it.
    return $value;
  }
  
  add_filter('act/update_value', 'eg_update_value');
  
?>
```

### act/update_value/{$property_type}

Update value for the specified property before it is saved to the database. Just remove `Property` from the property type key and then you have the right type for the filter.

Example: `PropertyString` => `act/update_value/string`

```
<?php

  function eg_update_value_string ($value, $post_id) {
    // do some magic with the value and return it.
    return $value;
  }

  add_filter('act/update_value/string', 'eg_update_value_string');

?>
```

### act/property/list/not_allowed_properties

Some properties aren't allowed to use on property list, like property map. So this filter can be used to return a string or array of properties that aren't okey to use.

```
<?php

  function eq_not_allowed_properties () {
    return 'PropertyKvack';
  }
  
  add_filter('act/property/list/not_allowed_properties', 'eq_not_allowed_properties');
  
?>
```