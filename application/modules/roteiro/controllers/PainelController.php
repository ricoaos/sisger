<?php

class Roteiro_PainelController extends App_Controller_Action
{

    public function init()
    {
        // $this->idGrupo = App_Identity::getGrupo();
        $this->mCliente = new Model_Cliente_Cliente();
    }

    public function indexAction()
    {
        $select = $this->mCliente->select()
        ->where('id_ativo !=?', '0')
        ->order('st_nome ASC');
        $rsCliente = $this->mCliente->fetchAll($select)->toArray();
        $this->view->rsCliente = $rsCliente;
        
        
        $mRoteiro = new Model_Roteiro_Roteiro();
        $rsRoteiro = $mRoteiro->fetchAll()->toArray();
        $this->view->rsRoteiro = $rsRoteiro;
    }
    
    
}