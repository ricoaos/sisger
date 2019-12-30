<?php

class Cadastro_ClienteController extends App_Controller_Action
{

    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->grupo = App_Identity::getGrupo();
        $this->idUsuario = App_Identity::getIdUsuario();
        $this->mCliente = new Model_Cliente_Cliente();
    }

    /**
     *
     * @throws Exception
     */
    public function indexAction()
    {
        $mRoteiro = new Model_Roteiro_Roteiro();
        $rsRoteiro = $mRoteiro->fetchAll()->toArray();
        $this->view->rsRoteiro = $rsRoteiro;
        
        $mFuncionario = new Model_Funcionario_Funcionario();
        $rsFuncionario = $mFuncionario->fetchAll()->toArray();
        $this->view->rsFuncionario = $rsFuncionario;
                
        if($this->_request->getParam('id'))
        {
            $args = self::getdadoscadastrados($this->_request->getParam('id'));
            $this->view->dadospagina = $args;
        }
        
        if($this->_request->isPost()){
            $post = $this->_request->getPost();
            $post['id_ativo'] = 1;
            $post['id_user_cadastro'] = $this->idUsuario;
            
            try {
                                
                if(empty($post["id_cliente"])){
                    $post['dt_cadastro']= date('Y-m-d H:i:s');
                    $rsCliente = $this->mCliente->insert($post);
                    $msg = "Registro gravado com sucesso.";
                }else{
                    $rsCliente = $post['id_cliente'];
                    $where = $this->mCliente->getAdapter()->quoteInto('id_cliente = ?', $rsCliente);
                    $this->mCliente->update($post,$where);
                    $msg = "Registro alterado com sucesso.";
                }
                
                $args = self::getdadoscadastrados($rsCliente);
                $this->view->dadospagina = $args;
                
            } catch (Zend_Db_Exception $e) {
                $e->rollBack();
                $msg= $e->getMessage();
            }
           
            $this->view->msg = $msg;
        }
    }

    /**
     * Enter description here ...
     */
    public function listagemAction()
    {
        $rsCliente = $this->mCliente->fetchAll()->toArray();
        $this->view->rsClientes = $rsCliente;
    }

    /**
     * Enter description here ...
     */
    public function inativarregistroAction()
    {
        if ($this->_request->getParam('id')) {
            list ($ativo, $id) = explode('@', base64_decode($this->_request->getParam('id')));
            $where = $this->mClienteCrupo->getAdapter()->quoteInto(array(
                'id_cliente = ?' => $id,
                'id_grupo=?' => $this->idGrupo
            ));
            $ativo = $ativo == 0 ? 1 : 0;
            
            $this->mClienteCrupo->update(array(
                'id_ativo' => $ativo
            ), $where);
            $this->_redirect('cliente/cliente/listagem');
        }
    }

    /**
     * Enter description here ...
     */
    public function getclientebycpfAction()
    {
        if ($this->_request->isPost()) {
            $cpf = $this->_request->getPost();
            
            $rsCpf = $this->mVcliente->fetchAll(array(
                'st_cpf = ?' => $cpf
            ))->toArray();
            
            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
            $this->getResponse()->setBody(json_encode(array(
                'result' => $rsCpf
            )));
        }
    }

   
    /**
     *
     * @param unknown $params            
     * @return string
     */
    public function getdadoscadastrados($params)
    {
        $dadospagina = $this->mCliente->fetchAll(array('id_cliente = ?' => $params ))->toArray();
        return $dadospagina[0];
    }
}