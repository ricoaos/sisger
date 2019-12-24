<?php

class Model_Funcionario_Funcionario extends Zend_Db_Table
{

    protected $_schema = 'cadastro';

    protected $_name = 'funcionario';

    protected $_primary = array(
        'id_funcionario'
    );
}