<?php

class Administrativo_PainelController extends App_Controller_Action
{
    public function init()
    {
        $this->idOrganizacao = App_Identity::getOrganizacao();
        $this->idGrupo = App_Identity::getGrupo();
    }
    
    public function indexAction()
    {        
       //teste de envio  de email
        /*$email_to = "ricoaos@gmail.com";
        $name_to = "AutoSys";
        $mail = new Zend_Mail ();
        $mail->addTo ($email_to, $name_to);
        $mail->setSubject ('Bem vindo ao Autosys Sistema de Gerenciamnto de Oficina');
        $mail->setBodyText ("Testando o envio de email pela aplicação");
        $mail->send();*/
    }
    
    public function getcount($params, $where=null)
    {
        $select = $params->select();
        $select->from($params,array('count(*) as count'));
        if($where == 1){
        
            $select->where('id_organizacao = ?' , $this->idOrganizacao);
        
        }
        if($where == 2){
            
            $select->where('id_grupo = ?' , $this->idGrupo);
            
        }
        $rows = $params->fetchAll($select);
        return($rows[0]);
    }
}