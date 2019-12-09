<?php

class Model_Usuario_UsuarioOrganizacao extends Zend_Db_Table
{
	protected $_schema  = 'usuario';
	protected $_name    = 'usuario_organizacao';
	protected $_primary = array('id_usuario', 'id_organizacao','id_perfil');
	
	public function getPerfilByParams($user, $grupo){
	
		$mUserOrg = new Model_Usuario_UsuarioOrganizacao();
		$select = $mUserOrg->select()->setIntegrityCheck(false)->distinct()
		->from(array('usorg' => 'usuario.usuario_organizacao'), array("usorg.*"))
		->join(array('org' => 'organizacao.organizacao'),'org.id_organizacao = usorg.id_organizacao', array("org.st_nome_fantasia"))
		->join(array('per' => 'usuario.perfil'),'per.id_perfil = usorg.id_perfil', array("per.st_nome"))
		->where('usorg.id_usuario = ?', $user)
		->where('usorg.id_grupo = ?', $grupo)
		->where('usorg.sn_ativo = ?', '1');
		return $mUserOrg->fetchAll($select);
	}
	
	/**
	 * 
	 * @param unknown $post
	 * @return number
	 */
	public function deleteReg($post)
	{
		$mUserOrg = new Model_Usuario_UsuarioOrganizacao();
		$where = array();
		$where[] = $mUserOrg->getAdapter()->quoteInto('id_organizacao = ?', $post['organizacao']);
		$where[] = $mUserOrg->getAdapter()->quoteInto('id_usuario = ?', $post['usuario']);
		return $mUserOrg->delete($where);
	}
}