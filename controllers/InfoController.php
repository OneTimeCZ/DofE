<?php

namespace controllers;

class InfoController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Info/index', 'base_template', [
            'active' => 'info',
            'title' => 'Informace'
        ]);
    }
}