<?php
/**
 * InfoController - Controlador para páginas informativas
 */
class InfoController extends Controller {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Muestra la página de ayuda
     */
    public function help() {
        $data = [
            'pageTitle' => 'Ayuda del Sistema'
        ];
        
        $this->view->render('info/help', $data);
    }
    
    /**
     * Muestra la página acerca de
     */
    public function about() {
        $data = [
            'pageTitle' => 'Acerca de ' . APP_NAME,
            'footerScripts' => []
        ];
        
        $this->view->render('info/about', $data);
    }
}