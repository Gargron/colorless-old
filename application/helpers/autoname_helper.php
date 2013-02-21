<?php

function format_namelist($array = array(), $limit = NULL, $to_print_var = NULL, $link = NULL) {
  $i = 0;
  $o = 0;
  $m = count($array);
  $output = "";
  foreach($array as $a) {
    $i++;
    if($to_print_var !== NULL) {
      $data = $a[$to_print_var];
    } else {
      $data = $a;
    }
    if($link !== NULL) {
      $data = anchor($link.$data, $data);
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