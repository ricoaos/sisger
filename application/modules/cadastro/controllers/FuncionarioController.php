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
        /*if($this->_request->getParam('id'))
        {
            list($date,$id) = explode('@',base64_decode($this->_request->getParam('id')));
            $args = self::getdadoscadastrados($id);
            
            if(!empty($args["st_foto"])){
                file_put_contents(APPLICATION_PATH . '/../public/img/fotos/usuario/'.$args['id_pessoa'].".png", base64_decode($args["st_foto"]));
            }
            
            $this->view->dadospagina = $args;
        }
        
        if($this->_request->getParam('id_similar'))
        {
            list($date,$id_pessoa) = explode('@',base64_decode($this->_request->getParam('id_similar')));
            $args = self::getdadospessoa($id_pessoa);
            
            if(!empty($args["st_foto"])){
                file_put_contents(APPLICATION_PATH . '/../public/img/fotos/usuario/'.$id_pessoa.".png", base64_decode($args["st_foto"]));
            }
            
            $this->view->dadospagina = $args;
        }
              
        //Realiza a inserção das informações
        if($this->_request->isPost())
        {
            $post = $this->_request->getPost();
            list($dd,$mm,$YY) = explode('/',$post["dt_nascimento"]);
            
            if(!empty($post["imagem"])){
                list($tipo,$conteudo) = explode(",", $post["imagem"]);
            }
            
            $mPessoa = new Model_Pessoa_Pessoa();
            $mFuncionario = new Model_Funcionario_Funcionario();
            $mFuncionarioOrg = new Model_Funcionario_FuncionarioOrganizacao();
                        
            $dados = array(                
                'st_nome'            => strtoupper($post["st_nome"]),
                'st_nome_sondex'     => soundex($post["st_nome"]),
                'st_nome_metaphone'  => metaphone($post["st_nome"]),
                'st_sexo'            => $post['st_sexo'],
                'st_fonecontato'     => preg_replace('/\D+/', '', $post["st_fonecontato"]),
                'dt_nascimento'      => $YY.'-'.$mm.'-'.$dd,
                'st_foto'           => !empty($post["imagem"]) ? 1 : (!empty($post['id_foto'])? $post['id_foto'] : null),
                'st_email'           => $post['st_email'],
                'st_cpf'             => preg_replace('/\D+/', '', $post["st_cpf"]),
                'id_tipo_pessoa'    => 1,
                'st_foto'           => !empty($post["imagem"]) ? $conteudo : (!empty($post['imagem'])? $post['imagem'] : null),
                'st_cep'             => preg_replace('/\D+/', '', $post["st_cep"]),
                'st_tipo_logradouro' => $post['st_tipo_logradouro'],
                'st_estado'          => $post['st_estado'],
                'st_logradouro'      => $post['st_logradouro'],
                'st_complemento'     => $post['st_complemento'],
                'st_numero'          => $post['st_numero'],
                'st_bairro'          => $post['st_bairro'],
                'st_cidade'          => $post['st_cidade'],               
            );
            
            try {
                
                if(empty($post["id_funcionario"])){
                    
                    $dtcadastro = date('Y-m-d H:i:s');
                    
                    if(empty($post["id_pessoa"])){
                        $dados['dt_cadastro']= $dtcadastro;
                        $rspessoa = $mPessoa->insert($dados);
                    }else{
                        $rspessoa = $post["id_pessoa"];
                    }
                    
                    list($dd,$mm,$YY) = explode('/',$post["dt_admissao"]);
                    
                    $args = array(
                        'id_pessoa'     => $rspessoa,
                        'id_ativo'      => 1,
                        'st_rg'         => $post['st_rg'],
                        'id_cargo'      => $post['id_cargo'],
                        'st_ctps'       => $post['st_ctps'],
                        'st_serie'      => $post['st_serie'],
                        'dt_admissao'   => $YY.'-'.$mm.'-'.$dd,
                        'vl_salario'    => $post['vl_salario'],
                        'id_banco'      => $post['id_banco'],
                        'st_agencia'    => $post['st_agencia'],
                        'st_conta'      => $post['st_conta'],
                        'id_conta_banco'=> $post['id_conta_banco'],
                        'ds_observacao' => $post['ds_observacao'],
                        'dt_cadastro'   => $dtcadastro);
                    
                    $rsfuncionario = $mFuncionario->insert($args);
                    
                    $params = array('id_funcionario' => $rsfuncionario, 'id_organizacao' => $this->idOrganizacao);
                    $rsFuncionarioOrg = $mFuncionarioOrg->insert($params);
                    
                }else{
                    
                    $where = $mPessoa->getAdapter()->quoteInto('id_pessoa = ?', $post["id_pessoa"]);
                    $mPessoa->update($dados,$where);
                    
                    $args = array(
                        'id_ativo'      => empty($post['id_ativo'])? 0 : $post['id_ativo'],
                        'st_rg'         => $post['st_rg'],
                        'id_cargo'      => $post['id_cargo'],
                        'st_ctps'       => $post['st_ctps'],
                        'st_serie'      => $post['st_serie'],
                        'dt_admissao'   => $YY.'-'.$mm.'-'.$dd,
                        'vl_salario'    => $post['vl_salario'],
                        'id_banco'      => $post['id_banco'],
                        'st_agencia'    => $post['st_agencia'],
                        'st_conta'      => $post['st_conta'],
                        'id_conta_banco'=> $post['id_conta_banco'],
                        'ds_observacao' => $post['ds_observacao']
                    );
                    
                    $where2 = $mFuncionario->getAdapter()->quoteInto('id_funcionario = ?', $post['id_funcionario']);
                    $mFuncionario->update($args,$where2);
                    
                    $rsfuncionario = $post["id_funcionario"];
                }
                
                $getdados = self::getdadoscadastrados($rsfuncionario);
                $this->view->dadospagina = $getdados;
                
                //Realiza o decode da imagem e grava no diretorio informado
                if(!empty($post["imagem"])){
                    $idFoto = empty($post["id_pessoa"]) ? $rspessoa : $post["id_pessoa"];
                    if(!file_put_contents(APPLICATION_PATH . '/../public/img/fotos/usuario/'.$idFoto.".png", base64_decode($getdados["st_foto"]))){
                        throw new Exception(1);
                    }
                }
                
                $msg=2;
                
            } catch (Zend_Db_Exception $e) {
                $e->rollBack();
                $msg= $e->getMessage();
            }
            
            $this->view->msg = $msg;
        }
    	
    	$mCargo = new Model_Apoio_Cargo();
    	$rsCargo = $mCargo->fetchAll()->toArray();
    	$this->view->rsCargo = $rsCargo;
    	
    	$mBanco = new Model_Apoio_Banco();
    	$rsBanco = $mBanco->fetchAll()->toArray();
    	$this->view->rsBanco = $rsBanco;
   
    	$mContaBanco = new Model_Apoio_ContaBanco();
    	$rsContaBanco = $mContaBanco->fetchAll()->toArray();
    	$this->view->rsContaBanco = $rsContaBanco;
    	
    	$mTipoLogradouro = new Model_Apoio_TipoLogradouro();
    	$rsTipoLogradouro = $mTipoLogradouro->fetchAll()->toArray();
    	$this->view->TipoLogradouro = $rsTipoLogradouro;*/
    }
     
    /**
     *
     * Enter description here ...
     */
    public function listagemAction()
    {
        //$mFuncionario = new Model_Funcionario_VwFuncionario();
        //$rsFuncionario = $mFuncionario->fetchAll(array('id_organizacao = ?' => $this->idOrganizacao), '',30)->toArray();
        $rsFuncionario = null;
        $this->view->rsFuncionario = $rsFuncionario;
    }
    
    /**
     *
     * Enter description here ...
     */
    public function inativarregistroAction()
    {
        if($this->_request->getParam('id'))
        {
            list($date,$id) = explode('@',base64_decode($this->_request->getParam('id')));
            $mfuncionario = new Model_Funcionario_Funcionario();
            $where = $mfuncionario->getAdapter()->quoteInto('id_funcionario = ?', $id );
            $mfuncionario->update(array('id_ativo'=> 0),$where);
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
    	$dadospagina = $mFuncionario->fetchAll(array('id_funcionario = ?' => $params))->toArray();
    	list($YY,$mm,$dd) = explode('-',$dadospagina[0]["dt_nascimento"]);
    	list($YYa,$mma,$dda) = explode('-',$dadospagina[0]["dt_admissao"]);
    	$dadospagina[0]["dt_nascimento"] = $dd.'/'.$mm.'/'.$YY;
    	$dadospagina[0]["dt_admissao"] = $dda.'/'.$mma.'/'.$YYa;
    	return $dadospagina[0];
    }
}