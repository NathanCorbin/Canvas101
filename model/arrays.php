<?php

    function merge_two_arrays($array1, $array2, $key) {
        $data = array();
        $arrayAB = array_merge($array1,$array2);
        foreach ($arrayAB as $value) {
            $id = $value[$key];
            
            if (!isset($data[$id])) {
                $data[$id] = array();
            }

            $data[$id] = array_merge($data[$id],$value);
        }

        return $data;
    }