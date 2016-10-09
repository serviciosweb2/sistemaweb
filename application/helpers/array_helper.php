<?php

/**
 * convierte un array proveniente de un result_array en un array simple
 * 
 * @param array $array
 * @return array
 */
function getArraySimple(array $array){
    $arrayRetorno = array();
    foreach ( $array as $key => $val ){
        $temp = array_values($val);
        $arrayRetorno[] = $temp[0];
    }
    return $arrayRetorno;
}

function mergeArrayWithDistinctValue($array1, $array2, $key) {
    $result = array();
    $array1 = array_merge($array1, $array2);

    foreach ($array1 as $a1) {
        $exists = false;
        foreach ($result as $r) {
            if($a1[$key] == $r[$key]) {
                $exists = true;
            }
        }
        if(!$exists) {
            $result[] = $a1;
        }
    }

    return $result;
}