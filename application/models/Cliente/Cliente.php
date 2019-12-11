<?php

class Model_Cliente_Cliente extends Zend_Db_Table
{

    protected $_schema = 'cliente';

    protected $_name = 'cliente';

    protected $_primary = array(
        'id_cliente'
    );
}