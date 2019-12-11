<?php

class Organizacao_OrganizacaoController extends App_Controller_Action
{

    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->mOrganizacao = new Model_Organizacao_Organizacao();
        $this->grupo = App_Identity::getGrupo();
    }

    /**
     * Insere e altera dados da organização
     * 
     * @throws ErrorException
     * @throws Exception
     */
    public function indexAction()
    {
        // Busca as informações cadastradas
        if ($this->_request->getParam('id')) {
            $this->view->dadospagina = self::getdadoscadastrados(base64_decode($this->_request->getParam('id')));
        }
        
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            // realiza o upload do logotipo caso ele seja enviado
            $_UP = array();
            $_UP['pasta'] = APPLICATION_PATH . '/../public/img/logo/';
            $_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
            $_UP['extensoes'] = array(
                'jpg',
                'png',
                'jpeg'
            );
            
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });
            
            $nmArquivo = null;
            if (file_exists($_FILES['arquivo']['tmp_name'])) {
                
                $arquivo = $_FILES['arquivo']['name'];
                list ($nome, $extensao) = explode('.', $arquivo);
                $nmArquivo = preg_replace('/\D+/', '', $post["st_cnpj"]) . '.' . $extensao;
                
                if (array_search($extensao, $_UP['extensoes']) === false)
                    throw new Exception("Por favor, envie arquivos com as seguintes extensões: jpg, jpeg ou png!");
                
                if ($_UP['tamanho'] < $_FILES['arquivo']['size'])
                    throw new Exception("O arquivo de upload é maior do que o limite de 2MB.");
                
                if (! move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nmArquivo))
                    throw new Exception("Não foi possível enviar o arquivo, tente novamente!");
            }
            
            // Formata dos dados para inserir ou alterar as informações
            $dados = array(
                'st_razao_social' => strtoupper($post['st_razao_social']),
                'st_nome_fantasia' => strtoupper($post['st_nome_fantasia']),
                'st_cnpj' => $post["st_cnpj"],
                'st_email' => $post['st_email'],
                'st_fonecontato' => preg_replace('/\D+/', '', $post["st_fonecontato"]),
                'st_cnes' => empty($post['st_cnes']) ? null : $post['st_cnes'],
                'st_responsavel' => strtoupper($post['st_responsavel']),
                'st_inscricao_estadual' => empty($post['st_inscricao_estadual']) ? null : preg_replace('/\D+/', '', $post["st_inscricao_estadual"]),
                'st_cep' => preg_replace('/\D+/', '', $post["st_cep"]),
                'st_tipo_logradouro' => $post['st_tipo_logradouro'],
                'st_estado' => $post['st_estado'],
                'st_logradouro' => $post['st_logradouro'],
                'st_complemento' => $post['st_complemento'],
                'st_numero' => $post['st_numero'],
                'st_bairro' => $post['st_bairro'],
                'st_cidade' => $post['st_cidade'],
                'st_observacao' => $post['st_observacao'],
                'id_municipio' => empty($post['id_municipio']) ? null : $post['id_municipio'],
                'id_ativo' => isset($post['id_ativo']) ? 1 : 0,
                'cd_grupo' => $this->grupo
            );
            
            try {
                if (isset($nmArquivo)) {
                    $dados['st_logo'] = $nmArquivo;
                }
                
                if (empty($post["id_organizacao"])) {
                    $dados['dt_cadastro'] = date('Y-m-d H:i:s');
                    $dados['id_ativo'] = 1;
                    if (! $rsOrganizacao = $this->mOrganizacao->insert($dados)) {
                        if (unlink($_UP['pasta'] . $nmArquivo))
                            $nmArquivo = null;
                        throw new Exception("Erro ao gravar registro, tente gravar novamente!");
                    }
                    
                    $this->view->dadospagina = self::getdadoscadastrados($rsOrganizacao);
                } else {
                    
                    $where = $this->mOrganizacao->getAdapter()->quoteInto('id_organizacao = ?', $post["id_organizacao"]);
                    if (! $rsOrganizacao = $this->mOrganizacao->update($dados, $where)) {
                        if (unlink($_UP['pasta'] . $nmArquivo))
                            $nmArquivo = null;
                        throw new Exception("Erro ao Alterar registro, tente alterar novamente!");
                    }
                    
                    $this->view->dadospagina = self::getdadoscadastrados($post["id_organizacao"]);
                }
                
                $msg = "Registro gravado com sucesso!";
            } catch (Exception $e) {
                $msg = $e->getMessage();
            }
            
            $this->view->msg = $msg;
        }
    }

    /**
     * Busca as informações para preenchimento do grid
     */
    public function listagemAction()
    {
        $rsOrganizacao = $this->mOrganizacao->fetchAll()->toArray();
        $this->view->rsOrganizacao = $rsOrganizacao;
    }

    /**
     * Inativa o registro
     */
    public function inativarregistroAction()
    {
        if ($this->_request->getParam('id')) {
            $where = $this->mOrganizacao->getAdapter()->quoteInto('id_organizacao = ?', base64_decode($this->_request->getParam('id')));
            $this->mOrganizacao->update(array(
                'id_ativo' => 0
            ), $where);
            $this->_redirect('organizacao/organizacao/listagem');
        }
    }

    /**
     * Função que busca as informações cadastradas de uma organização
     * 
     * @param unknown $params            
     * @return string
     */
    public function getdadoscadastrados($params)
    {
        $dadospagina = $this->mOrganizacao->fetchAll(array(
            'id_organizacao=?' => $params
        ))->toArray();
        return $dadospagina[0];
    }
}