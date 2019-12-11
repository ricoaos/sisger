<?php

class Sistema_CargoController extends App_Controller_Action
{

    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->grupo = App_Identity::getGrupo();
        $this->idUsuario = App_Identity::getIdUsuario();
        $this->mCargo = new Model_Sistema_Cargo();
    }

    public function indexAction()
    
    {
        if($this->_request->getParam('id'))
        { 
            $args = self::getdadoscadastrados($this->_request->getParam('id'));
            $this->view->dadospagina = $args;
        }
        
        if($this->_request->isPost())
        {  
            $post = $this->_request->getPost();            
            $post['id_ativo'] = 1;
            $post['id_user_cadastro'] = $this->idUsuario;
            try {
    
                if(empty($post["id_cargo"])){
        
                    $dtcadastro = date('Y-m-d H:i:s');
                    $post['dt_cadastro']= $dtcadastro;
                    $rsCargo = $this->mCargo->insert($post);
                }else{
                
            
                }
                
                $msg = "Registro gravado com sucesso.";

            } catch (Zend_Db_Exception $e) {
                $e->rollBack();
                $msg= $e->getMessage();
            }
    
            $this->view->msg = $msg;
            $this->_redirect('/sistema/cargo/listagem');
        }
    }

    /**
     * Enter description here ...
     */
    public function listagemAction()
    {
        $rsCargo = $this->mCargo->fetchAll()->toArray();
        $this->view->rsCargo = $rsCargo;
    }

    /**
     * Enter description here ...
     */
    public function inativarregistroAction()
    {
        if ($this->_request->getParam('id')) {
            list ($date, $id) = explode('@', base64_decode($this->_request->getParam('id')));
            $mfuncionario = new Model_Funcionario_Funcionario();
            $where = $mfuncionario->getAdapter()->quoteInto('id_funcionario = ?', $id);
            $mfuncionario->update(array(
                'id_ativo' => 0
            ), $where);
            $this->_redirect('funcionario/funcionario/listagem');
        }
    }
    

    /**
     *
     * @param unknown $params            
     * @return string
     */
    public function getdadoscadastrados($params)
    {
        $dadospagina = $this->mCargo->fetchAll(array('id_cargo = ?' => $params))->toArray();
        return $dadospagina[0];
    }
}