<?php

class Model_Sistema_Estadocivil extends Zend_Db_Table
{

    protected $_schema = 'sistema';

    protected $_name = 'estado_civil';

    protected $_primary = array(
        'id_estado_civil'
    );
}