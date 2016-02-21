<?php

namespace Controllers;

use Models\Article;
use Models\ArticleQuery;
use Models\Gallery;
use Models\GalleryQuery;
use Models\ImageGalleryMap;
use Models\ImageGalleryMapQuery;

class GalleryController extends Controller{

    public function index(){
        $galleries = GalleryQuery::create()
            ->joinWith('User')
            //shows only galleries that contain images
            ->useImageGalleryMapQuery()
                ->joinWith('Image')
            ->endUse()
            ->find();
        
        $this->view('Gallery/index', 'base_template', [
            'active' => 'gallery',
            'title' => 'Galerie',
            'recent' => ArticleQuery::recent(),
            'galleries' => $galleries
        ]);
    }
    
    public function single($id){
        $gallery = GalleryQuery::create()
            ->findPk($id);
        
        $images = $gallery->getImages();
        
        if(!$gallery){
            $this->addPopup('danger', 'Galerie se specifikovaným identifikačním číslem neexistuje.');
            redirectTo('/galerie');
        }
        
        $this->view('Gallery/single', 'base_template', [
            'active' => 'gallery',
            'title' => 'Galerie',
            'recent' => ArticleQuery::recent(),
            'gallery' => $gallery,
            'images' => $images,
            'js' => 'plugins/fotorama/fotorama',
            'css' => 'plugins/fotorama/fotorama'
        ]);
    }
}