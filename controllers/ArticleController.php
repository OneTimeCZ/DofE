<?php

namespace Controllers;

use Models\Article;
use Models\ArticleQuery;
use Models\Category;
use Models\CategoryQuery;
use Models\Comment;
use Models\CommentQuery;
use Models\Image;
use Models\ImageQuery;
use Models\User;
use Models\UserQuery;

class ArticleController extends Controller{
    
    public function showAllPage($id){
        $id = $id == 0  ? 1 : $id;
        
        $articles = ArticleQuery::create()
            ->orderByCreatedAt("desc")
            ->paginate($page = $id, $maxPerPage = 10);
        
        $this->view('Article/all', 'base_template', [
            'active' => 'blog',
            'title' => 'Blog',
            'articles' => $articles,
            'recent' => ArticleQuery::recent()
        ]);
    }

    public function showSingle($name){
        $id = explode('-', $name);
        $id = $id[0];
        
        $post = ArticleQuery::create()
            ->joinWith('Image')
            ->joinWith('User')
            ->joinWith('Category')
            ->findPk($id);
        
        if($post == NULL){
            //popup for nonexistent article
            redirectTo('/clanky');
        }
        
        $comments = CommentQuery::create()
            ->filterByIdArticle($id)
            ->joinWith('User')
            ->useUserQuery()
                ->joinWith('Image')
            ->endUse()
            ->orderByCreatedAt("desc")
            ->find();
        
        $this->view('Article/single', 'base_template', [
            'active' => 'blog',
            'title' => $post->getTitle(),
            'article' => $post,
            'comments' => $comments,
            'recent' => ArticleQuery::recent()
        ]);
        
    }
    
    public function showByCategoryPage($category, $id){
        $id = $id == 0  ? 1 : $id;
        
        $articles = ArticleQuery::create()
            ->useCategoryQuery()
                ->filterByUrl($category)
            ->endUse()
            ->joinWith("Category")
            ->orderByCreatedAt("desc")
            ->paginate($page = $id, $maxPerPage = 10);
        
        $this->view('Article/category', 'base_template', [
            'active' => 'blog',
            'title' => $articles->getFirst()->getCategory()->getName(),
            'recent' => ArticleQuery::recent(),
            'articles' => $articles
        ]);
    }
    
    public function comment($name){
        $id = explode('-', $name);
        $id = $id[0];
        
        if(!$this->isLogged()) {
            $this->addPopup('danger', 'Pro přidání komentáře musíte být přihlášeni.');
            redirectTo('/clanek/'.$id);
        }
        
        $comment = new Comment;
        $comment->setIdUser($_SESSION["user"]->getId());
        $comment->setIdArticle($id);
        $comment->setContent($_POST["comment_text"]);
        $comment->save();
        
        $this->addPopup('success', 'Váš komentář byl úspěšně přidán.');
        redirectTo('/clanek/'.$id);
    }
}