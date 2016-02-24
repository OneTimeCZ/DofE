<?php

namespace Controllers;

use Models\Article;
use Models\ArticleQuery;
use Models\User;
use Models\UserQuery;

class InfoController extends Controller{

    public function index(){
        $participants = UserQuery::create()
            ->joinWith("Member")
            ->joinWith("Image")
            ->find();
        
        $parents_faq = array(
            array("Co to vlastně je?", "Cow landjaeger pork chop pork loin prosciutto beef ribs turkey. Porchetta beef ribs sausage, picanha short ribs rump boudin spare ribs landjaeger. Sausage shank landjaeger rump prosciutto short ribs."),
            array("Kolik to stojí?", "Salami doner strip steak leberkas, ham hock flank alcatra shank. Pig rump hamburger doner venison. Fatback meatball sirloin, chicken pancetta picanha pig swine alcatra rump ground round jowl pork belly.")
        );
        
        $this->view('Info/index', 'base_template', [
            'active' => 'info',
            'title' => 'Informace',
            'recent' => ArticleQuery::recent(),
            'participants' => $participants,
            'parents_faq' => $parents_faq
        ]);
    }
}