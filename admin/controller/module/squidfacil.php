<?php

class ControllerModuleSquidfacil extends Controller {

    public function index() {
        $this->redirect($this->url->link('squidfacil/config', 'token=' . $this->session->data['token'], 'SSL'));        
    }

    public function install() {
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'module/squidfacil');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'module/squidfacil');
        
        $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'squidfacil/list');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'squidfacil/list');
        
        $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'squidfacil/import');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'squidfacil/import');
        
        $this->model_user_user_group->addPermission($this->user->getId(), 'access', 'squidfacil/config');
        $this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'squidfacil/config');
    }
    
    public function uninstall() {
        $this->load->model('user/user_group');

        $this->model_user_user_group->removePermission($this->user->getId(), 'access', 'module/squidfacil');
        $this->model_user_user_group->removePermission($this->user->getId(), 'modify', 'module/squidfacil');
        
        $this->model_user_user_group->removePermission($this->user->getId(), 'access', 'squidfacil/list');
        $this->model_user_user_group->removePermission($this->user->getId(), 'modify', 'squidfacil/list');
        
        $this->model_user_user_group->removePermission($this->user->getId(), 'access', 'squidfacil/import');
        $this->model_user_user_group->removePermission($this->user->getId(), 'modify', 'squidfacil/import');
        
        $this->model_user_user_group->removePermission($this->user->getId(), 'access', 'squidfacil/config');
        $this->model_user_user_group->removePermission($this->user->getId(), 'modify', 'squidfacil/config');
    }

}

?>
