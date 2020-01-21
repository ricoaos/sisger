<?php

class Roteiro_PainelController extends App_Controller_Action
{
    /**
     * 
     * {@inheritDoc}
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        // $this->idGrupo = App_Identity::getGrupo();
        $this->mCliente = new Model_Cliente_Cliente();
    }

    /**
     * 
     */
    public function indexAction()
    {
        $select = $this->mCliente->select()
        ->where('id_ativo !=?', '0')
        ->order('st_nome ASC');
        $rsCliente = $this->mCliente->fetchAll($select)->toArray();
        $this->view->rsCliente = $rsCliente;
    }
    
    /**
     * 
     */
    public function getroteirobyclienteAction()
    {
        $value = $this->getRequest()->getPost('id_cliente');
        $this->mVwClienteRoteiro = new Model_Roteiro_VwClienteRoteiro();
        $result = $this->mVwClienteRoteiro->fetchAll(array('id_cliente = ?' => $value ))->toArray();
        $result = json_encode(array('resultado'=>$result));
        $this->_helper->json($result);
    }
}