<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;
use Models\Image;
use Models\ImageQuery;
use Models\Category;
use Models\CategoryQuery;
use Models\Comment;
use Models\CommentQuery;
use Models\User;
use Models\UserQuery;
use Models\UserReport;
use Models\UserReportQuery;
use Models\BugReport;
use Models\BugReportQuery;
use Models\Idea;
use Models\IdeaQuery;
use Propel\Runtime\ActiveQuery\Criteria;

require_once '/helpers/helper.php';

class AdminController extends Controller{
    
    public function __construct(){
        parent::__construct();
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Do této sekce mají přístup pouze někteří přihlášení uživatelé. Pro vstup se prosíme přihlašte.');
            redirectTo('/');
        } elseif(!$this->isAdmin() && !$this->isEditor()){
            $this->addPopup('danger', 'Do této sekce bohužel nemáte přístup.');
            redirectTo('/');
        }
    }
    
    public function index(){  
        $users = UserQuery::create()
            ->filterByCreatedAt(array('min' => 'yesterday'))
            ->count();
        
        $comments = CommentQuery::create()
            ->filterByCreatedAt(array('min' => 'yesterday'))
            ->count();
        
        $articles = ArticleQuery::create()
            ->filterByCreatedAt(array('min' => 'yesterday'))
            ->count();
        
        $images = ImageQuery::create()
            ->filterByCreatedAt(array('min' => 'yesterday'))
            ->count();
        
        $user_reports = UserReportQuery::create()
            ->filterByCreatedAt(array('min' => 'yesterday'))
            ->count();
        
        $unsolved_bugs = BugReportQuery::create()
            ->filterByCreatedAt(array('min' => 'yesterday'))
            ->filterByFixedAt(NULL)
            ->count();
        
        $solved_bugs = BugReportQuery::create()
            ->filterByCreatedAt(array('min' => 'yesterday'))
            ->filterByFixedAt(NULL, Criteria::NOT_EQUAL)
            ->count();
        
        $ideas = IdeaQuery::create()
            ->filterByApprovedAt(NULL)
            ->count();
        
        $this->view('Admin/index', 'admin_template', [
            'active' => 'main',
            'title' => 'Administrace',
            'new' => [
                'users' => $users,
                'comments' => $comments,
                'articles' => $articles,
                'images' => $images,
                'user_reports' => $user_reports,
                'unsolved_bugs' => $unsolved_bugs,
                'solved_bugs' => $solved_bugs,
                'ideas' => $ideas
            ]
        ]);
    }
    
    public function articleList(){
        if($this->isAdmin()) {
            $articles = ArticleQuery::create()
                ->joinWith("User")
                ->orderByCreatedAt("desc")
                ->find();
            
            if($articles == NULL){
                $this->addPopup('danger', 'V databázi se nenechází žádný článek.');
            }
            
            $this->view('Admin/articleAdminList', 'admin_template', [
                'active' => 'list',
                'title' => 'Seznam článků',
                'articles' => $articles
            ]);
        } elseif($this->isEditor()) {
            $articles = ArticleQuery::create()
                ->filterByIdUser($_SESSION["user"]->getId())
                ->orderByCreatedAt("desc")
                ->find();
            
            if($articles->isEmpty()){
                $this->addPopup('danger', 'Ale ne! V databázi se nenechází žádný Vámi napsaný článek.');
            }
            
            $this->view('Admin/articleEditorList', 'admin_template', [
                'active' => 'list',
                'title' => 'Seznam článků',
                'articles' => $articles
            ]);
        }
    }
    
    public function articleAdd(){
        //Get a list of all available categories
        $categories = CategoryQuery::create()
            ->find();
        
        $this->view('Admin/addArticle', 'admin_template', [
            'active' => 'addArticle',
            'title' => 'Přidat nový článek',
            'js' => array('plugins/tinymce/tinymce.min', 'scripts/tinymceinit'),
            'categories' => $categories
        ]);
    }
    
    public function articleEdit($name){
        $id = explode('-', $name);
        $id = $id[0]; 
        
        $article = ArticleQuery::create()
            ->findPk($id);
        
        if(!$this->isAdmin()){
            if($article->getIdUser() != $_SESSION["user"]->getId()){
                $this->addPopup('danger', 'Pro úpravu cizích článků nemáte dostatečná práva.');
                redirectTo("/administrace/clanky");
            }
        }
        
        $categories = CategoryQuery::create()
            ->find();
        
        $this->view('Admin/editArticle', 'admin_template', [
            'active' => 'addArticle',
            'title' => 'Upravit článek',
            'js' => array('plugins/tinymce/tinymce.min', 'scripts/tinymceinit'),
            'categories' => $categories,
            'article' => $article
        ]);
    }
    
    public function articleDelete($name){
        
    }
    
    public function articleSave(){
        //If saving a new article
        if($_POST["save"]=="Přidat") {
            $article = new Article;
            $article->setIdUser($_POST["author"]);
            $article->setIdImage(1);
            $article->setIdCategory($_POST["category"]);
            $article->setTitle($_POST["title"]);
            $article->setUrl("2-test-url");
            $article->setKeywords(str_replace(", ", ",", $_POST["keywords"]));
            $article->setContent($_POST["content"]);
            
            $article->save();
            
            $this->addPopup('success', 'Článek byl úspěšně přidán!');
            redirectTo("/administrace");
        } elseif($_POST["save"]=="Upravit") {
            $article = ArticleQuery::create()
                ->findPk($_POST["article"]);
            $article->setIdCategory($_POST["category"]);
            $article->setTitle($_POST["title"]);
            $article->setKeywords(str_replace(", ", ",", $_POST["keywords"]));
            $article->setContent($_POST["content"]);
            
            $article->save();
            
            $this->addPopup('success', 'Článek byl úspěšně upraven!');
            redirectTo("/administrace/clanky");
        }
    }
    
    public function imageList(){
        
    }
    
    public function imageSave(){
        $fullsize_directory = "includes/images/fullsize/";
        $thumbnail_directory = "includes/images/thumbnails/";
        
        $file_name = token(20);
        $file_size = $_FILES['image']['size'];
        $file_temporary_name = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $file_extension = explode('.', $_FILES['image']['name']);
        $file_extension = strtolower($file_extension[count($file_extension) - 1]);
        $extensions = array("jpeg","jpg","png");
      
        if(getimagesize($file_temporary_name) === false) {
            $popups[] = array(
                'type' => 'danger',
                'content' => 'Tento soubor je prázdný. Prosíme nahrajte fotografii.'
            );
        }
        
        if(!in_array($file_extension, $extensions)){
            $popups[] = array(
                'type' => 'danger',
                'content' => 'Nepovolená přípona souboru. Prosíme nahrajte fotografii ve formátu JPEG nebo PNG.'
            );
        }
        
        if($file_size > 10485760){
            $popups[] = array(
                'type' => 'danger',
                'content' => 'Fotografie je přiliš rozměrná. Prosíme nahrajte fotografii o velikosti maximálně 10 MB.'
            );
        }
        
        if(strlen(utf8_decode($_POST["description"])) >= 500){
            $popups[] = array(
                'type' => 'danger',
                'content' => 'Popisek fotografie je příliš dlouhý. Popisek by měl obsahovat maximálně 500 znaků.'
            );
        }
        
        if(strlen(utf8_decode($_POST["title"])) >= 50){
            $popups[] = array(
                'type' => 'danger',
                'content' => 'Název fotografie je příliš dlouhý. Název by měl obsahovat maximálně 50 znaků.'
            );
        }
        
        if(isset($popups)){
            foreach($popups as $pop){
                $this->addPopup($pop["type"], $pop["content"]);
            }
            
            redirectTo("/administrace/fotografie/nahrat");
        }
        
        while(file_exists($fullsize_directory . $file_name) || file_exists($thumbnail_directory . $file_name)){
            $file_name = token(20);
        }
        
        $file_name .= "." . $file_extension;
        
        if(!move_uploaded_file($file_temporary_name, $fullsize_directory . $file_name)){
            $this->addPopup('danger', 'Při přesunu fotografie do složky fullsize nastal neočekávaný problém.');
            redirectTo("/administrace/fotografie/nahrat");
        }
        
        //copy image as a thumbnail, resize it afterwards
        
        $image = new Image;
        $image->setTitle($_POST["title"]);
        $image->setDescription($_POST["description"]);
        $image->setType("fullsize");
        $image->setPath($file_name);
        $image->setThumbnailPath($file_name);
        $image->save();
        
        $this->addPopup('success', 'Vaše fotografie byla úspěšně nahrána.');
        redirectTo("/administrace/fotografie/nahrat");
    }
    
    public function imageAdd(){
        $this->view('Admin/uploadPhoto', 'admin_template', [
            'active' => 'uploadPhoto',
            'title' => 'Nahrát fotografii'
        ]);
    }
    
    public function imageDelete($name){
        
    }
    
    public function commentList($name){
        $id = explode('-', $name);
        $id = $id[0];
        
        $comments = CommentQuery::create()
            ->joinWith("User")
            ->filterByIdArticle($id)
            ->orderByCreatedAt("desc")
            ->find();
        
        if($comments->isEmpty()){
            $this->addPopup('danger', 'K tomuto článku se v databázi nenachází žádný komentář.');
        }
            
        $this->view('Admin/commentList', 'admin_template', [
            'active' => 'commentList',
            'title' => 'Seznam komentářů',
            'article_id' => $id,
            'comments' => $comments
        ]);
    }
    
    public function commentDelete($name, $comment){
        $id = explode('-', $name);
        $article_id = $id[0];
        $comment_id = $comment;
        
        $comment = CommentQuery::create()
            ->findPk($comment_id)
            ->delete();
        
        $this->addPopup('success', 'Komentář byl úspěšně odstraněn!');
        redirectTo("/administrace/clanek/".$article_id."/komentare");
    }
}