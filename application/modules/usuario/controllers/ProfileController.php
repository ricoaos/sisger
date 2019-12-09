<?php

class Usuario_ProfileController extends App_Controller_Action
{
	public function init()
	{
		$this->idOrganizacao = App_Identity::getOrganizacao();
		$this->grupo = App_Identity::getGrupo();
		$this->idUsuario = App_Identity::getIdUsuario();
		$this->mUsuario = new Model_Usuario_Usuario();
	}
	
    public function indexAction()
    {        
    	
    	
    }
    
    
}