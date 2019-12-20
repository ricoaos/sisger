<?php

class Cadastro_FuncionarioController extends App_Controller_Action
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
        $mCargo = new Model_Sistema_Cargo();
        $rsCargo = $mCargo->fetchAll()->toArray();
        $this->view->rsCargo = $rsCargo;
        
        $mEstadocivil = new Model_Sistema_Estadocivil();
        $rsEstadocivil = $mEstadocivil->fetchAll()->toArray();
        $this->view->rsEstadocivil = $rsEstadocivil;
        
        if($this->_request->isPost()){
            $post = $this->_request->getPost();
            Zend_Debug::dump($post);
        }
        
    }

    /**
     * Enter description here ...
     */
    public function listagemAction()
    {
        // $mFuncionario = new Model_Funcionario_VwFuncionario();
        // $rsFuncionario = $mFuncionario->fetchAll(array('id_organizacao = ?' => $this->idOrganizacao), '',30)->toArray();
        $rsFuncionario = null;
        $this->view->rsFuncionario = $rsFuncionario;
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
        $mFuncionario = new Model_Funcionario_VwFuncionario();
        $dadospagina = $mFuncionario->fetchAll(array(
            'id_funcionario = ?' => $params
        ))->toArray();
        list ($YY, $mm, $dd) = explode('-', $dadospagina[0]["dt_nascimento"]);
        list ($YYa, $mma, $dda) = explode('-', $dadospagina[0]["dt_admissao"]);
        $dadospagina[0]["dt_nascimento"] = $dd . '/' . $mm . '/' . $YY;
        $dadospagina[0]["dt_admissao"] = $dda . '/' . $mma . '/' . $YYa;
        return $dadospagina[0];
    }
}