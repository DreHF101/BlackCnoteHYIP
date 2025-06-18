<?php

namespace Hyiplab\BackOffice;

class CoreController{

    public $viewPath;

    public function view($view,$data = [])
    {
        $viewContent = $this->loadView($view,$data);
        global $systemLayout;
        if ($systemLayout) {
            $layoutContent = $this->loadLayout($data,$systemLayout);
            echo str_replace("{{yield}}",$viewContent,$layoutContent);
        }else{
            echo  $viewContent;
        }
        $this->clearFlashSession();
        if (!is_admin()) {
            die;
        }
    }
    
    private function loadLayout($data,$systemViewLayout)
    {
        ob_start();
        extract($data);
        include $this->viewPath.'/'.$systemViewLayout.'.php';
        return ob_get_clean();
    }

    private function loadView($view,$data)
    {
        ob_start();
        extract($data);
        include $this->viewPath.'/'.$view.'.php';
        return ob_get_clean();
    }

    public function clearFlashSession()
    {
        if( empty(session_id()) && !headers_sent()){
            session_start();
        }
        $sessions = isset($_SESSION) ? $_SESSION : [];
        foreach ($sessions as $key => $session) {
            $isFlash = @explode('.____',$key)[1];
            if ($isFlash && $isFlash == 'flash') {
                hyiplab_session()->forget($key);
            }
        }
    }
}