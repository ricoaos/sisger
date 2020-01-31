<?php

class Sistema_SituacaoController extends App_Controller_Action
{
    
    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->grupo = App_Identity::getGrupo();
        $this->idUsuario = App_Identity::getIdUsuario();
        $this->mSituacao = new Model_Sistema_Situacao();
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
                
                if(empty($post["id_situacao"])){
                    $dtcadastro = date('Y-m-d H:i:s');
                    $post['dt_cadastro']= $dtcadastro;
                    $rsSituacao = $this->mSituacao->insert($post);
                    $msg = "Registro gravado com sucesso.";
                }else{
                    $rsSituacao = $_POST['id_situacao'];
                    $where = $this->mSituacao->getAdapter()->quoteInto('id_situacao = ?', $rsSituacao);
                    $this->mSituacao->update($post,$where);
                    $msg = "Registro alterado com sucesso.";
                }
            } catch (Zend_Db_Exception $e) {
                $e->rollBack();
                $msg= $e->getMessage();
            }
            
            $this->view->msg = $msg;
            $this->_redirect('/sistema/situacao/listagem');
        }
    }
    
    /**
     * Enter description here ...
     */
    public function listagemAction()
    {
        $rsSituacao = $this->mSituacao->fetchAll()->toArray();
        $this->view->rsSituacao = $rsSituacao;
    }
    
    /**
     * Enter description here ...
     */
    public function inativarregistroAction()
    {
        if ($this->_request->getParam('id')) {
            
            $where = $this->mSituacao->getAdapter()->quoteInto('id_situacao = ?', $this->_request->getParam('id'));
            $ativo = $this->_request->getParam('ativo');
            
            $this->mSituacao->update(array('id_ativo'=> $ativo),$where);
            $this->view->msg = "Registro alterado com sucesso.";
            $this->_redirect('/sistema/situacao/listagem');
        }
    }
    
    /**
     *
     * @param unknown $params
     * @return string
     */
    public function getdadoscadastrados($params)
    {
        $dadospagina = $this->mSituacao->fetchAll(array('id_situacao = ?' => $params))->toArray();
        return $dadospagina[0];
    }
}