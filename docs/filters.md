# Filters

### papi/load_value/{$property_type}

This filter is applied after the $value is loaded in the database. Just remove `Property` from the property type key and then you have the right type for the filter.

Example: `PropertyString` => `papi/load_value/string`

```
<?php

  function eg_load_value_string ($value, $post_id) {
    // do some magic with the value and return it.
    return $value;
  }

  add_filter('papi/load_value/string', 'eg_load_value_string');

?>
```

### papi/format_value/{$property_type}

Format the value of the property before we output it to the application. Just remove `Property` from the property type key and then you have the right type for the filter.

Example: `PropertyString` => `papi/format_value/string`

```
<?php

  function eg_format_value_string ($value, $post_id) {
    // do some magic with the value and return it.
    return $value;
  }

  add_filter('papi/format_value/string', 'eg_format_value_string');

?>
```

### papi/update_value/{$property_type}

This filter is applied before the $value is saved in the database. Just remove `Property` from the property type key and then you have the right type for the filter.

Example: `PropertyString` => `papi/update_value/string`

```
<?php

  function eg_update_value_string ($value, $post_id) {
    // do some magic with the value and return it.
    return $value;
  }

  add_filter('papi/update_value/string', 'eg_update_value_string');

?>
```

### papi/property/list/not_allowed_properties

Some properties aren't allowed to use on property list, like property map. So this filter can be used to return a string or array of properties that aren't okey to use.

```
<?php

  function eq_not_allowed_properties () {
    return 'PropertyKvack';
  }
  
  add_filter('papi/property/list/not_allowed_properties', 'eq_not_allowed_properties');
  
?>
```