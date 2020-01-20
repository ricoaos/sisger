<?php

class Model_Roteiro_VwClienteRoteiro extends Zend_Db_Table
{
    
    protected $_schema = 'roteiro';
    
    protected $_name = 'vw_cliente_roteiro';
    
    protected $_primary = array(
        'id_cliente'
    );
}