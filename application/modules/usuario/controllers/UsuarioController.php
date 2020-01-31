<?php

class Usuario_UsuarioController extends App_Controller_Action
{

    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->grupo = App_Identity::getGrupo();
        $this->idUsuario = App_Identity::getIdUsuario();
        $this->mUsuario = new Model_Usuario_Usuario();
        $this->mUserOrganizacao = new Model_Usuario_UsuarioOrganizacao();
        $this->mVwUsuario = new Model_Usuario_VwUsuario();
    }

    public function indexAction()
    {
        // Busca as informações cadastradas
        if ($this->_request->getParam('id')) {
            list ($date, $id) = explode('@', base64_decode($this->_request->getParam('id')));
            $this->view->dadospagina = self::getdadoscadastrados($id);
        }
        
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $dtcadastro = date('Y-m-d H:i:s');
            $dados = array(
                'st_usuario' => strtolower($post['st_usuario']),
                'st_senha' => md5($post["password"]),
                'id_grupo' => $this->grupo,
                'id_funcionario' => strtoupper($post['id_funcionario']),
                'id_usuario_cadastro' => $this->idUsuario,
                'id_organizacao_atual' => $this->idOrganizacao
            );
            
            if (empty($post['id_usuario'])) {
                $dados['dt_cadastro'] = $dtcadastro;
                $dados['id_ativo'] = 1;
                $rsUsuario = $this->mUsuario->insert($dados);
                $this->view->dadospagina = self::getdadoscadastrados($rsUsuario);
            } else {
                
                $dados['id_ativo'] = $post['id_ativo'];
                if (empty($post["password"])) {
                    unset($dados['st_senha']);
                }
                
                $where = $this->mUsuario->getAdapter()->quoteInto('id_usuario = ?', $post["id_usuario"]);
                $this->mUsuario->update($dados, $where);
                $this->view->dadospagina = self::getdadoscadastrados($post["id_usuario"]);
            }
        }
        
       /* $mOrganizacao = new Model_Organizacao_Organizacao();
        $rsOrganizacao = $mOrganizacao->fetchAll(array(
            'cd_grupo = ?' => $this->grupo
        ))->toArray();
        $this->view->organizacao = $rsOrganizacao;*/
        
        $mPerfil = new Model_Usuario_Perfil();
        $rsPerfis = $mPerfil->fetchAll(array('id_perfil != ?' => 1))->toArray( );
        $this->view->rsPerfil = $rsPerfis;
        
       /* $mFuncionario = new Model_Funcionario_VwFuncionario();
        $rsFuncionario = $mFuncionario->fetchAll(array(
            'id_organizacao = ?' => $this->idOrganizacao
        ), '', 30)->toArray();
        $this->view->rsFuncionario = $rsFuncionario;*/
    }

    /**
     */
    public function acessoAction()
    {
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $dtcadastro = date('Y-m-d H:i:s');
            $mUsuarioOrg = new Model_Usuario_UsuarioOrganizacao();
            $rsPerfis = $mUsuarioOrg->fetchAll(array(
                'id_usuario=?' => $post['id_usuario'],
                'id_organizacao=?' => $post['id_organizacao'],
                'id_grupo=?' => $this->grupo
            ))->toArray();
            
            if (! empty($rsPerfis)) {
                $msg = "Já existe um cadastro com esses parâmetros";
            } else {
                
                $dados = array(
                    'id_usuario' => $post['id_usuario'],
                    'id_organizacao' => $post['id_organizacao'],
                    'id_perfil' => $post['id_perfil'],
                    'id_grupo' => $this->grupo,
                    'sn_ativo' => 1,
                    'dt_cadastro' => $dtcadastro,
                    'id_usuario_cadastro' => $this->idUsuario
                );
                
                $mUsuarioOrg->insert($dados);
                $data = array('id_organizacao_atual' => $post['id_organizacao']);
                $where = $this->mUsuario->getAdapter()->quoteInto('id_usuario = ?', $post["id_usuario"]);
                $this->mUsuario->update($data, $where);
                
                $msg = "Cadastrado";
            }
            
            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender();
            $this->getResponse()->setBody(json_encode(array('result' => $msg)));
        }
    }

    /**
     */
    public function deleteperfilAction()
    {
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            $mUsuarioOrg = new Model_Usuario_UsuarioOrganizacao();
            $rsPerfil = $mUsuarioOrg->deleteReg($post);
            $this->getHelper('viewRenderer')->setNoRender();
        }
    }

    /**
     * Enter description here ...
     */
    public function listagemAction()
    {
        $rsUsuario = $this->mUsuario->fetchAll(array(), '', 30)->toArray();
        $this->view->rsUsuario = $rsUsuario;
    }

    /**
     *
     * @param unknown $params            
     * @return string
     */
    public function getdadoscadastrados($params)
    {
        $dadospagina = $this->mUsuario->fetchAll(array('id_usuario = ?' => $params))->toArray();
        return $dadospagina[0];
    }
}