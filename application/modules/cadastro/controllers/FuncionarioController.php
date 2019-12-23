<?php

class Cadastro_FuncionarioController extends App_Controller_Action
{

    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->grupo = App_Identity::getGrupo();
        $this->idUsuario = App_Identity::getIdUsuario();
        $this->mFuncionario = new Model_Funcionario_Funcionario();
    }

    public function indexAction()
    {
        $mCargo = new Model_Sistema_Cargo();
        $rsCargo = $mCargo->fetchAll()->toArray();
        $this->view->rsCargo = $rsCargo;
        
        $mEstadocivil = new Model_Sistema_Estadocivil();
        $rsEstadocivil = $mEstadocivil->fetchAll()->toArray();
        $this->view->rsEstadocivil = $rsEstadocivil;
        
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
            
            if(isset($_FILES["ds_assinatura"]["name"])){
                
                date_default_timezone_set('America/Sao_Paulo');
                
                try{
                    
                    $target_dir = APPLICATION_PATH .'../uploads/';
                    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                    $uploadOk = 1;
                    
                    $_UP = array();
                    $_UP['tamanho'] = 1024 * 1024 * 1; // 1Mb
                    $_UP['extensoes'] = array('jpg', 'png', 'gif','pdf');
                    
                    $_UP['erro'][0] = 'Não houve erro';
                    $_UP['erro'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                    $_UP['erro'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                    $_UP['erro'][3] = 'O upload do arquivo foi feito parcialmente';
                    $_UP['erro'][4] = 'Não foi feito o upload do arquivo';
                    
                    $extensao = strtolower(end(explode('.', $_FILES['fileToUpload']['name'])));
                    
                    set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
                        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
                    });
                        
                    if ($_FILES['fileToUpload']['error'] != 0) {
                        self::logs($_FILES["fileToUpload"],'Não foi possivel fazer o upload do arquivo,'.$_UP['erro'][$_FILES['fileToUpload']['error']]);
                        throw new Exception("Não foi possivel fazer o upload do arquivo, ".'<b>'. $_UP['erro'][$_FILES['fileToUpload']['error']].'</strong>');
                    }
                    
                    if (array_search($extensao, $_UP['extensoes']) === false){
                        self::logs($_FILES["fileToUpload"],'Extensão de arquivo não permitido para essa operação');
                        throw new Exception( "Por favor, envie arquivos com as seguintes extensões: jpg, png, gif, pdf");
                    }
                    
                    if ($_UP['tamanho'] < $_FILES['fileToUpload']['size']){
                        self::logs($_FILES["fileToUpload"],'O arquivo de upload é maior do que o limite de 1MB.');
                        throw new Exception("O arquivo de upload é maior do que o limite de 1MB.");
                    }
                    if (!self::validType($_FILES['fileToUpload'])){
                        self::logs($_FILES["fileToUpload"],'Minetype não altorizado ou modificado');
                        throw new Exception("Minetype não altorizado ou modificado.");
                    }
                    
                    $arquivo = strtolower(file_get_contents($_FILES["fileToUpload"]['tmp_name']));
                    $pattern = '/^<(\?php|script|notXSS)|(<\/.\w*>$)|<\w*\s*on....(?=s|\=)|(\?php|echo|alert|javascript|cmd|system|request|\?>$|<\?\s)/mi';
                    if (preg_match_all($pattern, $arquivo, $matches, PREG_SET_ORDER, 0)){
                        self::logs($_FILES["fileToUpload"],'Arquivo com código malicioso');
                        throw new Exception( 'Arquivo com código malicioso!');
                    }
                    
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file) || $uploadOk != 0) {
                        echo "Upload do arquivo ".'<strong>'. $_FILES["fileToUpload"]["name"].'</strong>'. " realizado com sucesso.";
                        self::logs($_FILES["fileToUpload"], 'upload realizado com sucesso');
                    } else {
                        throw new Exception("Desculpe, houve um erro ao enviar seu arquivo.");
                    }
                        
                }catch (Exception $e) {
                    echo 'Exception => ',  utf8_encode($e->getMessage()), "\n";
                }
            }
            
            try {
                
                if(empty($post["id_cargo"])){
                    $dtcadastro = date('Y-m-d H:i:s');
                    $post['dt_cadastro']= $dtcadastro;
                    $rsFuncionario = $this->mFuncionario->insert($post);
                    $msg = "Registro gravado com sucesso.";
                }else{
                    $rsFuncionario = $_POST['id_funcionario'];
                    $where = $this->mFuncionario->getAdapter()->quoteInto('id_funcionario = ?', $rsFuncionario);
                    $this->mFuncionario->update($post,$where);
                    $msg = "Registro alterado com sucesso.";
                }
            } catch (Zend_Db_Exception $e) {
                $e->rollBack();
                $msg= $e->getMessage();
            }
            
            $this->view->msg = $msg;
            $this->_redirect('/cadastro/funcionario/listagem');
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
    
    
    function logs($file,$mensagem){
        
        $texto = date('d/m/Y H:i:s').' ARQUIVO: '.$file['name'].', TIPO: '.$file['type'].', RESULT: '.$mensagem;
        error_log($texto . PHP_EOL, 3, APPLICATION_PATH .'../uploads/log'.date("dmY").'.txt');
    }
    
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