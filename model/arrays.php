<?php

    /**
     * Takes two different arrays and merges them together
     * given a certain key
     * 
     * If two arrays share the same key, both arrays will get
     * merged together without overwriting anything
     * 
     * @param $array1 the first array
     * @param $array2 the second array
     * @param $key the key to look for to combine the arrays at
     */
    function merge_two_arrays($array1, $array2, $key) {
        $data = array();
        $both = array_merge($array1, $array2);

        foreach ($both as $value) {
            $id = $value[$key];
            
            if (!isset($data[$id])) {
                $data[$id] = array();
            }

            $data[$id] = array_merge($data[$id],$value);
        }

        return $data;
    }