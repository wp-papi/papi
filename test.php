<?php

function radixsort (&$a) {
  $n = count($a);
  $partition = array();
  for ($slot = 0; $slot < 256; ++$slot) {
    $partition[] = array();
  }
  for ($i = 0; $i < $n; ++$i) {
    $partition[$a[$i]->sort_order & 0xFF][] = &$a[$i];
  }
  $i = 0;
  for ($slot = 0; $slot < 256; ++$slot) {
    for ($j = 0, $n = count($partition[$slot]); $j < $n; ++$j) {
      $a[$i++] = &$partition[$slot][$j];
    }
  }
}

function array_sort ($array, $key) {
  $result = array();
  $sorter = array();

  if (empty($array)) {
    return $result;
  }

  foreach ($array as $k => $value) {
    if (is_array($value)) {
      foreach ($value as $j => $v) {
        if ($j == $key) {
          $sorter[$j] = $v;
        }
      }
    } else {
      $sorter[$k] = $value;
    }
  }

  asort($sorter);

  foreach ($sorter as $k => $value) {
    $result[$k] = $array[$k];
  }

  return $result;
}

function array_sort2($array, $on, $order=SORT_ASC)
{

$new_array = array();
$sortable_array = array();

if (count($array) > 0) {
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $k2 => $v2) {
                if ($k2 == $on) {
                    $sortable_array[$k] = $v2;
                }
            }
        } else {
            $sortable_array[$k] = $v;
        }
    }

    switch ($order) {
        case SORT_ASC:
            asort($sortable_array);
        break;
        case SORT_DESC:
            arsort($sortable_array);
        break;
    }

    foreach ($sortable_array as $k => $v) {
        $new_array[$k] = $array[$k];
    }
}

return $new_array;
}
function order_array_num ($array, $key, $order = "ASC")
{
    $tmp = array();
    foreach($array as $akey => $array2)
    {
        $tmp[$akey] = $array2[$key];
    }

    if($order == "DESC")
    {arsort($tmp , SORT_NUMERIC );}
    else
    {asort($tmp , SORT_NUMERIC );}

    $tmp2 = array();
    foreach($tmp as $key => $value)
    {
        $tmp2[$key] = $array[$key];
    }

    return $tmp2;
}

function _ptb_sort_order ($array, $key = 'sort_order') {
  if (empty($array)) {
    return array();
  }

  $sorter = array();

  foreach ($array as $k => $value) {
    if (is_object($value)) {
      $sorter[$k] = $value->$key;
    } else {
      $sorter[$k] = $value[$key];
    }
  }

  asort($sorter, SORT_NUMERIC);

  $result = array();

  foreach ($sorter as $k => $v) {
    $value = $array[$k];

    if ((is_object($value) && !isset($value->$key)) || (is_array($value) && !isset($value[$key]))) {
      $rest[] = $value;
    } else {
      $result[$k] = $array[$k];
    }
  }

  $result = array_values($result);

  foreach ($rest as $key => $value) {
    $result[] = $value;
  }

  return $result;
}

$res = _ptb_sort_order(array(
  (object)array('sort_order' => null, 'key' => 2),
  (object)array('sort_order' => 0, 'key' => 1),
  array('sort_order' => 3, 'key' => 4),
  array('sort_order' => 2, 'key' => 3),
  array('sort_order' => 3, 'key' => 5)
));

echo '<pre>';
var_dump($res);
