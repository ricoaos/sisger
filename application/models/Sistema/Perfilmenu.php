<?php

class Model_Sistema_Perfilmenu extends Zend_Db_Table 
{	
    protected $_schema  = 'usuario';
    protected $_name    = 'vw_perfil_usuario';
    protected $_primary = array('id_usuario', 'id_organizacao');    
}

