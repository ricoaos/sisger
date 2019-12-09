<?php

class Model_Usuario_VwUsuario extends Zend_Db_Table
{
	protected $_schema  = 'usuario';
	protected $_name    = 'vw_usuario';
	protected $_primary = array('id_usuario');
}