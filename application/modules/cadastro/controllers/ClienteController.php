<?php

class Cadastro_ClienteController extends App_Controller_Action
{

    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->grupo = App_Identity::getGrupo();
        $this->idUsuario = App_Identity::getIdUsuario();
        $this->mCliente = new Model_Cliente_Cliente();
        $this->mRoteiroCliente = new Model_Roteiro_RoteirosCliente();
        $this->mVwCliente = new Model_Roteiro_VwClienteRoteiro();
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

        if(!empty($_FILES["ds_rt"]["name"])){
                        
            date_default_timezone_set('America/Sao_Paulo');
            
            try{
                
                $target_dir = APPLICATION_PATH . '/../public/upload/';
                
                $target_file = $target_dir . date("dmYHis").'_' . basename($_FILES["ds_rt"]["name"]);
                $uploadOk = 1;
                
                $_UP = array();
                $_UP['tamanho'] = 1024 * 1024 * 1; // 1Mb
                $_UP['extensoes'] = array('jpg', 'png', 'gif','pdf');
                
                $_UP['erro'][0] = 'Não houve erro';
                $_UP['erro'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                $_UP['erro'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                $_UP['erro'][3] = 'O upload do arquivo foi feito parcialmente';
                $_UP['erro'][4] = 'Não foi feito o upload do arquivo';
                
                $extensao = strtolower(end(explode('.', $_FILES['ds_rt']['name'])));
                
                set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
                    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
                });
                    
                    if ($_FILES['ds_rt']['error'] != 0) {
                        self::logs($_FILES["ds_rt"],'Não foi possivel fazer o upload do arquivo,'.$_UP['erro'][$_FILES['ds_rt']['error']]);
                        throw new Exception("Não foi possivel fazer o upload do arquivo, ".'<b>'. $_UP['erro'][$_FILES['ds_rt']['error']].'</strong>');
                    }
                    
                    if (array_search($extensao, $_UP['extensoes']) === false){
                        self::logs($_FILES["ds_rt"],'Extensão de arquivo não permitido para essa operação');
                        throw new Exception( "Por favor, envie arquivos com as seguintes extensões: jpg, png, gif, pdf");
                    }
                    
                    if ($_UP['tamanho'] < $_FILES['ds_rt']['size']){
                        self::logs($_FILES["ds_rt"],'O arquivo de upload é maior do que o limite de 1MB.');
                        throw new Exception("O arquivo de upload é maior do que o limite de 1MB.");
                    }
                    if (!self::validType($_FILES['ds_rt'])){
                        self::logs($_FILES["ds_rt"],'Minetype não altorizado ou modificado');
                        throw new Exception("Minetype não altorizado ou modificado.");
                    }
                    
                    $arquivo = strtolower(file_get_contents($_FILES["ds_rt"]['tmp_name']));
                    $pattern = '/^<(\?php|script|notXSS)|(<\/.\w*>$)|<\w*\s*on....(?=s|\=)|(\?php|echo|alert|javascript|cmd|system|request|\?>$|<\?\s)/mi';
                    if (preg_match_all($pattern, $arquivo, $matches, PREG_SET_ORDER, 0)){
                        self::logs($_FILES["ds_rt"],'Arquivo com código malicioso');
                        throw new Exception( 'Arquivo com código malicioso!');
                    }
                    
                    if (move_uploaded_file($_FILES["ds_rt"]["tmp_name"], $target_file) || $uploadOk != 0) {
                        self::logs($_FILES["ds_rt"], 'upload realizado com sucesso');
                    } else {
                        throw new Exception("Desculpe, houve um erro ao enviar seu arquivo.");
                    }
                    
            }catch (Zend_Db_Exception $e) {
                $msg= utf8_encode($e->getMessage());
                $e->rollBack();
            }
        }
                
        if($this->_request->isPost()){
            
            $post = $this->_request->getPost();
            $post["ds_rt"] = !empty($post["ds_rt"]) ? basename($_FILES["ds_rt"]["name"]).'_'.date("dmYHis") : (empty($post['imageRT']) ? null : $post['imageRT']);
            
            $args = array(
                "id_ativo" => 1,
                "tp_pessoa" => $post['tp_pessoa'],
                "st_nome" => $post['st_nome'],
                "num_cpf_cnpj" => $post['num_cpf_cnpj'],
                "ds_email" => $post['ds_email'],
                "num_telefone" => $post['num_telefone'],
                "num_celular" => $post['num_celular'],
                "num_cep" => $post['num_cep'],
                "ds_logradouro" => $post['ds_logradouro'],
                "num_logradouro" => $post['num_logradouro'],
                "ds_complemento" => $post['ds_complemento'],
                "ds_bairro" => $post['ds_bairro'],
                "ds_cidade" => $post['ds_cidade'],
                "ds_uf" => $post['ds_uf'],
                "id_funcionario" => $post['id_funcionario'],
                "ds_cliente_contrato" => $post['ds_cliente_contrato'],
                "ds_observacao" => $post['ds_observacao'],
                "id_user_cadastro" => $this->idUsuario,
                "ds_rt" => $post['ds_rt']
            );
    
            try {
                                
                if(empty($post["id_cliente"])){
                    $args['dt_cadastro']= date('Y-m-d H:i:s');
                    $rsCliente = $this->mCliente->insert($args);
                    $msg = "Registro gravado com sucesso.";
                    
                }else{
                                        
                    $rsCliente = $post['id_cliente'];
                    $where = $this->mCliente->getAdapter()->quoteInto('id_cliente = ?', $rsCliente);
                    $this->mCliente->update($args,$where);
                    $msg = "Registro alterado com sucesso.";
                    
                    $where2 = $this->mCliente->getAdapter()->quoteInto('id_cliente = ?', $rsCliente);
                    $this->mRoteiroCliente->delete($where2);
                }
                    
                foreach($post['id_roteiro'] as $values){
                    $dados = array(
                        'id_cliente' => $rsCliente,
                        'id_roteiro' => $values
                    );
                    
                    $this->mRoteiroCliente->insert($dados);
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
     */
    public function getclientesAction()
    {
        $result = $this->mCliente->fetchAll()->toArray();
        $result = json_encode(array('resultado'=>$result));
        $this->_helper->json($result);
    }
   
    /**
     *
     * @param unknown $params            
     * @return string
     */
    public function getdadoscadastrados($params)
    {
        $dadospagina = $this->mVwCliente->fetchAll(array('id_cliente = ?' => $params ))->toArray();
        return $dadospagina;
    }
    
    /**
     *
     * @param unknown $file
     * @param unknown $mensagem
     */
    function logs($file,$mensagem){
        
        $texto = date('d/m/Y H:i:s').' ARQUIVO: '.$file['name'].', TIPO: '.$file['type'].', RESULT: '.$mensagem;
        error_log($texto . PHP_EOL, 3, APPLICATION_PATH . '/../public/upload/log'.date("dmY").'.txt');
    }
    
    /**
     *
     * @param unknown $file
     * @return boolean
     */
    function validType($file) {
        
        $listValidFiles = array(
            'pdf' => 'application/pdf',
            'csv' => 'text/csv',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'png' => 'image/png',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.ms-excel',
        );
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $type = array_search($finfo->file($file['tmp_name']), $listValidFiles, true);
        $pos = strpos($file['name'], '.');
        $extension = str_replace('.','',substr($file['name'], $pos));
        if (false !== $type && $listValidFiles[$type]==$file['type'] && $type==strtolower($extension)) {
            return true;
        }
        return false;
    }
}