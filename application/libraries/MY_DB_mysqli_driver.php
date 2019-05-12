<?php

class MY_DB_mysqli_driver extends CI_DB_mysqli_driver {

    function __construct($params) {
        parent::__construct($params);
        log_message('debug', 'Extended DB driver class instantiated!');
    }

    function getOne($table = '', $where = null) {
        return $this->get_where($table, $where, 1);
    }

    function customJoin($table1 = '', $table2 = '', $key = '', $type = 'INNER') {
        return $this->join($table1, $table1 . '.' . $key . ' = ' . $table2 . '.' . $key, strtoupper( $type ) );
    }

}
?>