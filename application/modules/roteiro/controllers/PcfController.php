<?php

class Roteiro_PcfController extends App_Controller_Action
{

    public function init()
    {
        // $this->idGrupo = App_Identity::getGrupo();
        
    }

    public function indexAction()
    {
        if($this->_request->getParam('id'))
        {
            
            $this->mCliente = new Model_Cliente_Cliente();
            $rsCliente = $this->mCliente->fetchAll(array('id_cliente = ?' => $this->_request->getParam('id') ))->toArray();
            $this->view->rsCliente = $rsCliente;
            //Zend_Debug::dump(App_Identity);
            //die;
        }

    }
}