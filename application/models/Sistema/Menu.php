<?php

class Model_Sistema_Menu extends Zend_Db_Table
{

    protected $_schema = 'sistema';

    protected $_name = 'menu';

    protected $_primary = array(
        'id_menu'
    );
}