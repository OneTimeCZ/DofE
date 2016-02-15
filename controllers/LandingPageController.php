<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;
use Models\Image;
use Models\ImageQuery;
use Models\User;
use Models\UserQuery;
use Models\Quote;
use Models\QuoteQuery;
use Models\Member;
use Models\MemberQuery;

class LandingPageController extends Controller{
    
    public function index(){
        $participants = array(2, 1);
        
        $users = UserQuery::create()
            ->filterByIdMember($participants)
            ->joinWith('Image')
            ->joinWith('Member')
            ->useMemberQuery()
                ->joinWith('Quote')
            ->endUse()
            ->find();
        
        $this->view('Landing/index', 'base_template', [
            'active' => 'landing',
            'title' => 'Hlavní stránka',
            'recent' => ArticleQuery::recent(),
            'js' => 'plugins/fotorama/fotorama',
            'css' => 'plugins/fotorama/fotorama',
            'users' => $users
        ]);
    }
}