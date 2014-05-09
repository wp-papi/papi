# Filters

### ptb_property_before_save

Every property array that should be saved will go through this filter. The array will have two values. One telling you which property type it is and one with the value.

```
<?php

  function eg_property_before_save ($property) {
    
    // Property type. E.g "PropertyString".
    $type = $property['type'];
    
    // The value. E.g "Hello, world".
    $value = $property['value'];
    
    return $property;
  }
  
  add_filter('ptb_property_before_save', 'eg_property_before_save');
  
?>
```