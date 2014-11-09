<?php

    /**
    * Helper that help retrive data and protect them.
    */
    class Sanitize {

        /**
         * Try to recover params from $_GET and $_POST
         * $params is an Array with the parameter name as a $key and a default value as $value
         */
        function get_params(Array $params, $error_on_invalid = true, $error_on_missing = true) {
            $parameters = array();
            foreach ($params as $param => $default_value) {
                if ( isset($_REQUEST[$param]) ) {
                    if ( settype( $_REQUEST[$param], gettype($default_value) ) ) {
                        $parameters[$param] = $_REQUEST[$param];
                    } else {
                        if ($error_on_invalid) {
                            throw new Exception("Error Invalid Parameters");
                        } else {
                            $parameters[$param] = $_REQUEST[$param];
                        }
                    }
                } else {
                    if ($error_on_missing) {
                        throw new Exception("Error Missing Parameters");
                    } else {
                        $parameters[$param] = $default_value;
                    }
                }
            }
            return $parameters;
        }

    }

?>
