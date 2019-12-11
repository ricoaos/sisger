<?php

class Model_Sistema_Cargo extends Zend_Db_Table
{

    protected $_schema = 'sistema';

    protected $_name = 'cargo';

    protected $_primary = array(
        'id_cargo'
    );
}