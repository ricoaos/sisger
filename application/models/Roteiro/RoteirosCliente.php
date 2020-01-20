<?php

class Model_Roteiro_RoteirosCliente extends Zend_Db_Table
{
    
    protected $_schema = 'roteiro';
    
    protected $_name = 'roteiros_cliente';
    
    protected $_primary = array('id_roteiro', 'id_cliente' );
}