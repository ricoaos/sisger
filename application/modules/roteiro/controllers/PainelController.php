<?php

class Roteiro_PainelController extends App_Controller_Action
{

    public function init()
    {
        // $this->idGrupo = App_Identity::getGrupo();
    }

    public function indexAction()
    {
        $mRoteiro = new Model_Roteiro_Roteiro();
        $rsRoteiro = $mRoteiro->fetchAll()->toArray();
        $this->view->rsRoteiro = $rsRoteiro;
    }
}