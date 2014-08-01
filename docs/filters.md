# Filters

### ptb/update_value

Update value for every property before it is saved to the database.

Example:

```
<?php

  function eg_update_value ($value, $post_id) {
    // do some magic with the value and return it.
    return $value;
  }
  
  add_filter('ptb/update_value', 'eg_update_value');
  
?>
```

### ptb/update_value/{$property_type}

Update value for the specified property before it is saved to the database. Just remove `Property` from the property type key and then you have the right type for the filter.

Example: `PropertyString` => `ptb/update_value/string`

```
<?php

  function eg_update_value_string ($value, $post_id) {
    // do some magic with the value and return it.
    return $value;
  }

  add_filter('ptb/update_value/string', 'eg_update_value_string');

?>
```