<?php

function format_namelist($array = array(), $limit = NULL, $to_print_var = NULL, $link = NULL, $link_var = NULL) {
  $i = 0;
  $o = 0;
  $m = count($array);
  $output = "";
  foreach($array as $a) {
    $i++;
    if($to_print_var !== NULL) {
      $a    = (array) $a;
      $data = $a[$to_print_var];
    } else {
      $data = $a;
    }
    if($link !== NULL) {
      if ($link_var == NULL) {
        $data = anchor($link.$data, $data);
      } else {
        $link_data = $a[$link_var];
        $data = anchor($link.$link_data, $data);
      }
    }
    if($limit !== NULL && $i > $limit) {
      $o++;
    } else {
      if($i == $m || $m == 1) {
        $output .= $data;
      } elseif ($i < $m && $i !== ($m-1) && $m > 1) {
        $output .= $data . ", ";
      } elseif ($i == ($m-1)) {
        $output .= $data . " and ";
      }
    }
  }
  if($limit !== NULL && $o > 0) {
    $output .= " and " . $o . " other" . ($o == 1 ? NULL : $o > 1 ? "s" : NULL);
  }
  return $output;
}

?>