<?php
function is_int_number($var){
    return (is_numeric($var) && (intval($var) == floatval($var)));
}

function reArrayFiles(&$file_post){
    $file_array = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++){
        foreach ($file_keys as $key){
            $file_array[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_array;
}

function isValidEmail($email){
    return preg_match('#^.+@[^.]+(\..+)?$#', $email);
}

function scrand($min,$max) {
    $range = $max - $min + 1;
    if ($range == 0) return $min;
    $length = (int) (log($range,2) / 8) + 1;
    $max = pow(2, 8 * $length);
    $num = $max + 1;
    while ($num > $max) {
        $num = hexdec(bin2hex(openssl_random_pseudo_bytes($length,$s)));
    }
    return ($num  % $range) + $min;
}
?>