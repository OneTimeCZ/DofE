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
use Models\Member;
use Models\MemberQuery;
use Models\MembershipApplication;
use Models\MembershipApplicationQuery;
use Models\Gallery;
use Models\GalleryQuery;
use Models\ImageGalleryMap;
use Models\ImageGalleryMapQuery;
use Models\Ban;
use Models\BanQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use \DateTime;

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
            ->filterByCreatedAt(array('min' => time() - (7*24*60*60)))
            ->count();
        
        $comments = CommentQuery::create()
            ->filterByCreatedAt(array('min' => time() - (7*24*60*60)))
            ->count();
        
        $articles = ArticleQuery::create()
            ->filterByCreatedAt(array('min' => time() - (7*24*60*60)))
            ->count();
        
        $images = ImageQuery::create()
            ->filterByType('fullsize')
            ->filterByCreatedAt(array('min' => time() - (7*24*60*60)))
            ->count();
        
        $user_reports = UserReportQuery::create()
            ->filterByCreatedAt(array('min' => time() - (7*24*60*60)))
            ->count();
        
        $unsolved_bugs = BugReportQuery::create()
            ->filterByFixedAt(NULL)
            ->count();
        
        $applications = MembershipApplicationQuery::create()
            ->filterByState("pending")
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
                'applications' => $applications,
                'ideas' => $ideas,
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
            'js' => array('plugins/tinymce/tinymce.min', 'scripts/tinymceinit', 'plugins/croppie/croppie', 'scripts/croppie-article'),
            'css' => array('plugins/croppie/croppie'),
            'categories' => $categories
        ]);
    }
    
    public function articleEdit($name){
        $id = explode('-', $name);
        $id = $id[0]; 
        
        $article = ArticleQuery::create()
            ->findPk($id);
        
        if($article == NULL) {
            $this->addPopup('danger', 'Článek se specifikovaným identifikačním číslem se v databázi nenachází.');
            redirectTo('/administrace/clanky');
        }
        
        if(!$this->isAdmin()){
            if($article->getIdUser() != $_SESSION["user"]->getId()){
                $this->addPopup('danger', 'Pro úpravu cizích článků nemáte dostatečná práva.');
                redirectTo("/administrace/clanky");
            }
        }
        
        $categories = CategoryQuery::create()
            ->find();
        
        $this->view('Admin/editArticle', 'admin_template', [
            'active' => 'list',
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
            
            if(isset($_POST["image-article"])){
                $data = explode(',', $_POST["image-article"]);
                if(count($data) == 2 && $data[0] == "data:image/png;base64" && base64_decode($data[1])){
                    $dir = "includes/images/fullsize/";
                    $img = imagecreatefromstring(base64_decode($data[1]));
                    do {
                        $name = token(20).".png";
                        $path = $dir.$name;
                    }
                    while(file_exists($path));

                    $i = new Image();
                    $i->setPath($name)
                        ->setThumbnailPath($name)
                        ->setType("fullsize")
                        ->save();
                    imagepng($img, $path);

                    $dir = "includes/images/960x540/";
                    $path = $dir.$name;
                    imagepng(resizeImg($img, 960, 540), $path);

                    $dir = "includes/images/50x50/";
                    $path = $dir.$name;
                    imagepng(resizeImg($img, 50, 50), $path);
                }

                else {
                    $this->addPopup('danger', 'Něco se pokazilo při nahrávání obrázku. Zkuste to prosím znovu.');
                    redirectTo('/administrace/clanky/pridat');
                }
            } else {
                $this->addPopup('danger', 'Něco se pokazilo při nahrávání obrázku. Zkuste to prosím znovu.');
                redirectTo('/administrace/clanky/pridat');
            }
            
            $im = ImageQuery::create()
                ->filterByPath($name)
                ->findOne();
            $article->setIdImage($im->getId());
            $article->setIdCategory($_POST["category"]);
            $article->setTitle($_POST["title"]);
            $article->setKeywords(str_replace(", ", ",", $_POST["keywords"]));
            $article->setContent($_POST["content"]);
            
            $article->save();
            
            $article = ArticleQuery::create()->orderByCreatedAt('desc')->findOne();
            $article->setUrl($article->getId());
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
            
            if(isset($_POST["image-article"])){
                $data = explode(',', $_POST["image-article"]);
                if(count($data) == 2 && $data[0] == "data:image/png;base64" && base64_decode($data[1])){
                    $dir = "includes/images/fullsize/";
                    $img = imagecreatefromstring(base64_decode($data[1]));
                    do {
                        $name = token(20).".png";
                        $path = $dir.$name;
                    }
                    while(file_exists($path));

                    $i = new Image();
                    $i->setPath($name)
                        ->setThumbnailPath($name)
                        ->setType("fullsize")
                        ->save();
                    imagepng($img, $path);

                    $dir = "includes/images/960x540/";
                    $path = $dir.$name;
                    imagepng(resizeImg($img, 960, 540), $path);

                    $dir = "includes/images/50x50/";
                    $path = $dir.$name;
                    imagepng(resizeImg($img, 50, 50), $path);
                    
                    $im = ImageQuery::create()
                        ->filterByPath($name)
                        ->findOne();

                    $article->setIdImage($im->getId());
                }

                else {
                    $this->addPopup('danger', 'Něco se pokazilo při nahrávání obrázku. Zkuste to prosím znovu.');
                    redirectTo('/administrace/clanek/' . $id . '/upravit');
                }
            }
            
            $article->save();
            
            $this->addPopup('success', 'Článek byl úspěšně upraven!');
            redirectTo("/administrace/clanky");
        }
    }
    
    public function imageSave(){
        
        //name -> token(20)
        //fullsize dir = fullsize
        //create a thumbnail from the fullsize image
        //thumbnail dir = thumbnails
    }
    
    public function imageAdd(){
        $this->view('Admin/uploadPhoto', 'admin_template', [
            'active' => 'uploadPhoto',
            'title' => 'Nahrát fotografii'
        ]);
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
    
    public function ideaRefuse($id){
        if(!($this->isAdmin() || $this->isEditor())){
            $this->addPopup('danger', 'Pro zamítávání návrhů nemáte dostatečná práva.');
            redirectTo("/");
        }
        
        $idea = IdeaQuery::create()
            ->findPk($id);
        
        if(!isset($idea)){
            $this->addPopup('danger', 'Návrh s tímto identifikačním číslem se v databázi nenachází.');
            redirectTo("/administrace");
        }
        
        if($idea->getIdUser() == $_SESSION["user"]->getId()){
            $this->addPopup('danger', 'Tento návrh jste podali vy, nemůžete ho proto zároveň zamítnout.');
            redirectTo("/administrace");
        }
        
        $idea->delete();
        
        $this->addPopup('success', 'Návrh číslo ' . $id . ' byl úspěšně zamítnut a smazán.');
        redirectTo("/administrace");
    }
    
    public function ideaApprove($id){
        if(!($this->isAdmin() || $this->isEditor())){
            $this->addPopup('danger', 'Pro schvalování návrhů nemáte dostatečná práva.');
            redirectTo("/");
        }
        
        $idea = IdeaQuery::create()
            ->findPk($id);
        
        if(!isset($idea)){
            $this->addPopup('danger', 'Návrh s tímto identifikačním číslem se v databázi nenachází.');
            redirectTo("/administrace");
        }
        
        if($idea->getIdUser() == $_SESSION["user"]->getId()){
            $this->addPopup('danger', 'Tento návrh jste podali vy, nemůžete ho proto zároveň schválit.');
            redirectTo("/administrace");
        }
        
        $idea->setApprovedAt(time());
        $idea->setApprovedBy($_SESSION["user"]->getId());
        $idea->save();
        
        $this->addPopup('success', 'Návrh číslo ' . $id . ' byl úspěšně schválen.');
        redirectTo("/administrace");
    }
    
    public function newUsersPage(){
        $users = UserQuery::create()
            ->filterByCreatedAt(array('min' => time() - 7*24*60*60))
            ->orderByUsername()
            ->find();
        
        if($users->isEmpty()){
            $this->addPopup('danger', 'Za poslední týden se nezaregistrovali žádní noví uživatelé.');
        }
        
        $this->view('Admin/newUsers', 'admin_template', [
            'active' => 'newUsers',
            'title' => 'Noví uživatelé',
            'users' => $users
        ]);
    }
    
    public function newCommentsPage(){
        $comments = CommentQuery::create()
            ->filterByCreatedAt(array('min' => time() - 7*24*60*60))
            ->joinWith('Article')
            ->joinWith('User')
            ->orderByCreatedAt('desc')
            ->find();
        
        if($comments->isEmpty()){
            $this->addPopup('danger', 'Za poslední týden nebyly přidány žádné komentáře.');
        }
        
        $this->view('Admin/newComments', 'admin_template', [
            'active' => 'newComments',
            'title' => 'Nové komentáře',
            'comments' => $comments
        ]);
    }
    
    public function newArticlesPage(){
        $articles = ArticleQuery::create()
            ->filterByCreatedAt(array('min' => time() - 7*24*60*60))
            ->joinWith('User')
            ->orderByCreatedAt('desc')
            ->find();
        
        if($articles->isEmpty()){
            $this->addPopup('danger', 'Za poslední týden nebyly přidány žádné články.');
        }
        
        $this->view('Admin/newArticles', 'admin_template', [
            'active' => 'newArticles',
            'title' => 'Nové články',
            'articles' => $articles
        ]);
    }
    
    public function newPhotosPage(){
        $images = ImageQuery::create()
            ->filterByCreatedAt(array('min' => time() - 7*24*60*60))
            ->filterByType('fullsize')
            ->orderByCreatedAt('desc')
            ->find();
        
        if($images->isEmpty()){
            $this->addPopup('danger', 'Za poslední týden nebyly přidány žádné fotografie.');
        }
        
        $this->view('Admin/newImages', 'admin_template', [
            'active' => 'newImages',
            'title' => 'Nové fotografie',
            'images' => $images
        ]);
    }
    
    public function solvedBugsPage(){
        $solved_bugs = BugReportQuery::create()
            ->filterByFixedAt(NULL, Criteria::NOT_EQUAL)
            ->orderByCreatedAt('desc')
            ->find();
        
        if($solved_bugs->isEmpty()){
            $this->addPopup('danger', 'Na Vaší stránce zatím nebyl vyřešen žádný hlášený problém.');
        }
        
        $this->view('Admin/solvedBugs', 'admin_template', [
            'active' => 'solvedBugs',
            'title' => 'Vyřešené chyby',
            'solved_bugs' => $solved_bugs
        ]);
    }
    
    public function unsolvedBugsPage(){
        $unsolved_bugs = BugReportQuery::create()
            ->filterByFixedAt(NULL)
            ->orderByCreatedAt('desc')
            ->find();
        
        if($unsolved_bugs->isEmpty()){
            $this->addPopup('success', 'Gratulujeme! Na Vaší stránce se momentálně nenachází žádné nevyřešené problémy. Nebo alespoň žádné nebyly nahlášeny ;)');
        }
        
        $this->view('Admin/unsolvedBugs', 'admin_template', [
            'active' => 'unsolvedBugs',
            'title' => 'Nevyřešené chyby',
            'unsolved_bugs' => $unsolved_bugs
        ]);
    }
    
    public function singleBugPage($id){
        $bug = BugReportQuery::create()
            ->joinWith('User')
            ->findPk($id);
        
        if($bug == NULL){
            $this->addPopup('danger', 'Hlášení o chybě s tímto identifikačním číslem se v databázi nenachází.');
            redirectTo('/administrace');
        }
        
        $this->view('Admin/singleBugPage', 'admin_template', [
            'active' => 'singleBug',
            'title' => 'Hlášení o chybě',
            'bug' => $bug
        ]);
    }
    
    public function markBugSolved($id){
        $bug = BugReportQuery::create()
            ->findPk($id);
        
        if($bug == NULL){
            $this->addPopup('danger', 'Hlášení o chybě s tímto identifikačním číslem se v databázi nenachází.');
            redirectTo('/administrace');
        }
        
        if(!$this->isAdmin()){
            $this->addPopup('danger', 'Pro změnu stavu chyby na stránce nemáte dostatečná práva.');
            redirectTo('/administrace');
        }
        
        $bug->setFixedAt(time());
        $bug->save();
        
        $this->addPopup('success', 'Stav chyby #' . $id . ' byl nastaven na "opraveno".');
        redirectTo("/administrace");
    }
    
    public function deleteBug($id){
        $bug = BugReportQuery::create()
            ->findPk($id);
        
        if($bug == NULL){
            $this->addPopup('danger', 'Hlášení o chybě s tímto identifikačním číslem se v databázi nenachází.');
            redirectTo('/administrace');
        }
        
        if(!$this->isAdmin()){
            $this->addPopup('danger', 'Pro odstranění chyby na stránce nemáte dostatečná práva.');
            redirectTo('/administrace');
        }
        
        $bug->setFixedAt(time());
        $bug->save();
        
        $this->addPopup('success', 'Chyba #' . $id . ' byla odstraněna.');
        redirectTo("/administrace");
    }
    
    public function userReportsPage(){
        $user_reports = UserReportQuery::create()
            ->joinUserAuthor('UserAuthor')
            ->with('UserAuthor', 'UserAuthor')
            ->joinUserReported('UserReported')
            ->with('UserReported', 'UserReported')
            ->filterByCreatedAt(array('min' => time() - 7*24*60*60))
            ->orderByCreatedAt('desc')
            ->find();
        
        if($user_reports->isEmpty()){
            $this->addPopup('danger', 'V databázi se nenachází žádní nahlášení uživatelé.');
            redirectTo("/administrace");
        }
        
        $this->view('Admin/userReportsPage', 'admin_template', [
            'active' => 'userReports',
            'title' => 'Nahlášení uživatelé',
            'user_reports' => $user_reports
        ]);
    }
    
    public function singleUserReportPage($id){
        $user_report = UserReportQuery::create()
            ->joinUserAuthor('UserAuthor')
            ->with('UserAuthor', 'UserAuthor')
            ->joinUserReported('UserReported')
            ->with('UserReported', 'UserReported')
            ->findPk($id);
        
        if($user_report == NULL){
            $this->addPopup('danger', 'Nahlášení uživatele s tímto identifikačním číslem se v databázi nenachází.');
            redirectTo("/administrace/nahlaseni-uzivatele");
        }
        
        $this->view('Admin/singleUserReportPage', 'admin_template', [
            'active' => 'singleUserReport',
            'title' => 'Nahlášení uživatele',
            'user_report' => $user_report
        ]);
    }
    
    public function deleteUserReport($id){
        if(!$this->isAdmin()){
            $this->addPopup('danger', 'Pro odstranění nahlášení nemáte dostatečná práva.');
            redirectTo('/administrace/nahlaseni-uzivatele');
        }
        
        $report = UserReportQuery::create()
            ->findPk($id);
        
        if($report == NULL) {
            $this->addPopup('danger', 'Nahlášení uživatele se zadaným identifikačním číslem se v databázi nenachází.');
            redirectTo('/administrace/nahlaseni-uzivatele');
        }
        
        $report->delete();
        
        $this->addPopup('success', 'Nahlášení uživatele bylo úspěšně odstraněno.');
        redirectTo('/administrace/nahlaseni-uzivatele');
    }
    
    public function banUser($id){
        if(!$this->isAdmin()){
            $this->addPopup('danger', 'Pro zakázání přístupu nemáte dostatečná práva.');
            redirectTo('/administrace/nahlaseni-uzivatele');
        }
        
        $user = UserQuery::create()
            ->findPk($id);
        
        if($user == NULL) {
            $this->addPopup('danger', 'Uživatel se zadaným identifikačním číslem se v databázi nenachází.');
            redirectTo('/administrace/nahlaseni-uzivatele');
        }
        
        $ban = new Ban;
        $ban->setReason($_POST["reason"]);
        $ban->setIdUser($id);
        $end_date = new DateTime();
        $end_date->setTimestamp($end_date->format('U') + $_POST["length"]);
        $ban->setEndingDate($end_date->format('U'));
        $ban->setBannedBy($_SESSION["user"]->getId());
        $ban->save();
        
        $reports = UserReportQuery::create()
            ->filterByIdUserReported($id)
            ->find();
        
        foreach($reports as $r) {
            $r->delete();
        }
        
        $this->addPopup('success', 'Uživateli ' . $user->getUsername() . ' byl úspěšně zakázán přístup.');
        redirectTo('/administrace/nahlaseni-uzivatele');
    }
    
    public function ideaSuggestionsPage(){
        $ideas = IdeaQuery::create()
            ->filterByApprovedAt(NULL)
            ->joinUserAuthor('UserAuthor')
            ->with('UserAuthor', 'UserAuthor')
            ->find();
        
        if($ideas->isEmpty()){
            $this->addPopup('danger', 'Momentálně se v databázi nenachází žádné nepřijaté návrhy na zlepšení.');
            redirecTo("/administrace");
        }
        
        $this->view('Admin/ideasPage', 'admin_template', [
            'active' => 'ideasPage',
            'title' => 'Návrhy na zlepšení',
            'ideas' => $ideas
        ]);
    }
    
    public function singleIdeaSuggestionPage($id){
        $idea = IdeaQuery::create()
            ->joinUserAuthor('UserAuthor')
            ->with('UserAuthor', 'UserAuthor')
            ->joinUserApproved('UserApproved')
            ->with('UserApproved', 'UserApproved')
            ->findPk($id);
        
        if($idea == NULL){
            $this->addPopup('danger', 'Návrh na zlepšení s tímto identifikačním číslem se v databázi nenachází.');
            redirecTo("/administrace");
        }
        
        $this->view('Admin/singleIdeaPage', 'admin_template', [
            'active' => 'singleIdeaPage',
            'title' => 'Návrh na zlepšení',
            'idea' => $idea
        ]);
    }
    
    public function membershipApplications(){
        $apps = MembershipApplicationQuery::create()
            ->filterByState("pending")
            ->joinWith("User")
            ->orderByCreatedAt("desc")
            ->find();
            
        if($apps->isEmpty()){
            $this->addPopup("danger", "V databázi se momentálně nenachází žádné žádosti o členství.");
        }
        
        $this->view('Admin/membershipApplications', 'admin_template', [
            'active' => 'membershipApplications',
            'title' => 'Žádosti o členství',
            'apps' => $apps
        ]);
    }
    
    public function acceptMembershipApplication($id){
        $app = MembershipApplicationQuery::create()
            ->findPk($id);
        
        if($app == NULL){
            $this->addPopup("danger", "Žádost o členství se zadaným identifikačním číslem se v databázi nenachází.");
            redirectTo("/administrace/zadosti-o-clenstvi");
        }
        
        if($app->getState() != "pending"){
            $this->addPopup("danger", "Žádost o členství se zadaným identifikačním číslem již byla schválena nebo zamítnuta.");
            redirectTo("/administrace/zadosti-o-clenstvi");
        }
        
        $app->setState('accepted');
        $app->setAcceptedAt(time());
        $app->save();
        
        $token = token(30);
        $member = new Member;
        $member->setName($app->getName());
        $member->setSurname($token);
        $member->setMemberFrom($app->getCreatedAt());
        $member->save();
            
        $member_id = MemberQuery::create()
            ->filterBySurname($token)
            ->findOne();
        
        $member_id->setSurname($app->getSurname());
        $member_id->save();
        
        $user = UserQuery::create()
            ->findPk($app->getIdUser());
        $user->setIdMember($member_id->getId());
        if($user->getPermissions() == 0)$user->setPermissions(1);
        $user->save();
        
        $this->addPopup("success", "Žádost o členství byla úspěšně přijata. Uživateli byl vytvořen DofE účet.");
        redirectTo("/administrace/zadosti-o-clenstvi");
    }
    
    public function rejectMembershipApplication($id){
        $app = MembershipApplicationQuery::create()
            ->findPk($id);
        
        if($app == NULL){
            $this->addPopup("danger", "Žádost o členství se zadaným identifikačním číslem se v databázi nenachází.");
            redirectTo("/administrace/zadosti-o-clenstvi");
        }
        
        if($app->getState() != "pending"){
            $this->addPopup("danger", "Žádost o členství se zadaným identifikačním číslem již byla schválena nebo zamítnuta.");
            redirectTo("/administrace/zadosti-o-clenstvi");
        }
        
        $app->setState('rejected');
        $app->save();
        
        $this->addPopup("success", "Žádost o členství byla úspěšně zamítnuta.");
        redirectTo("/administrace/zadosti-o-clenstvi");
    }
    
    public function galleryList() {        
        if($this->isEditor()) {
            $galleries = GalleryQuery::create()
                ->filterByIdUser($_SESSION["user"]->getId())
                ->find();
        } elseif($this->isAdmin()) {
            $galleries = GalleryQuery::create()
                ->find();
        }
        
        if($galleries->isEmpty()) {
            $this->addPopup('danger', 'V databázi se momentálně nenachází žádná galerie.');
        }
        
        $this->view('Admin/galleryList', 'admin_template', [
            'active' => 'galleryList',
            'title' => 'Seznam galerií',
            'galleries' => $galleries
        ]);
    }
    
    public function imageList($id){
        
    }
    
    public function imageDelete($name){
        
    }
    
    public function galleryPage($id) {
        $images = ImageGalleryMapQuery::create()
            ->filterByIdGallery($id)
            ->joinWith('Image')
            ->joinWith('Gallery')
            ->find();
        
        if($images->isEmpty()){
            $this->addPopup('danger', 'Galerie se zadaným identifikačním číslem se v databázi nenachází.');
            redirectTo('/administrace/galerie');
        }
        
        $this->view('Admin/galleryPage', 'admin_template', [
            'active' => 'galleryPage',
            'title' => 'Galerie',
            'images' => $images,
            'id' => $id
        ]);
    }
    
    public function newGalleryPage() {
        $this->view('Admin/addGallery', 'admin_template', [
            'active' => 'addGallery',
            'title' => 'Přidat galerii',
            'js' => array('scripts/croppie-gallery')
        ]);
    }
    
    public function saveGallery($id) {
        
    }
    
    public function userList() {
        if(!$this->isAdmin()){
            $this->addPopup('danger', 'Pro změnu práv uživatelů nemáte dostatečná práva.');
            redirectTo('/administrace');
        }
        
        $all = UserQuery::create()
            ->filterByPermissions(3, Criteria::NOT_EQUAL)
            ->find();
        
        foreach($all as $a){
            if($a->getPermissions() == 2){
                $editors[] = $a;
            } else {
                $users[] = $a;
            }
        }
        
        if(!isset($editors)){
            $this->addPopup('danger', 'V databázi se momentálně nenachází žádný redaktor.');
        }
        
        if(!isset($users)){
            $this->addPopup('danger', 'V databázi se momentálně nenachází žádný uživatel.');
        }
        
        $this->view('Admin/userList', 'admin_template', [
            'active' => 'users',
            'title' => 'Seznam uživatelů',
            'editors' => $editors,
            'users' => $users
        ]);
    }
    
    public function setPermissions($username, $permissions) {
        if(!$this->isAdmin()){
            $this->addPopup('danger', 'Pro změnu práv uživatelů nemáte dostatečná práva.');
            redirectTo('/administrace');
        }
        
        $user = UserQuery::create()
            ->filterByUsername($username)
            ->findOne();
        
        if(!isset($user)){
            $this->addPopup('danger', 'Uživatel se zadaným uživatelským jménem neexistuje.');
            redirectTo('/administrace/uzivatele');
        }
        
        if($permissions == 1) {
            if($user->getPermissions() == 2 || $user->getPermissions() == 3) {
                $this->addPopup('danger', 'Uživatel se zadaným uživatelským jménem již nemůže být dále povýšen.');
                redirectTo('/administrace/uzivatele');
            } else {
                $user->setPermissions(2);
                $user->save();
                $this->addPopup('success', 'Uživatel ' . $user->getUsername() . ' byl úspěšné povýšen na redaktora.');
                redirectTo('/administrace/uzivatele');
            }
        } else {
            if($user->getPermissions() == 2 || $user->getPermissions() == 3) {
                if($user->getIdMember() != NULL) {
                    $user->setPermissions(1);
                } else {
                    $user->setPermissions(2);
                }
                $user->save();
                
                $this->addPopup('success', 'Uživatel ' . $user->getUsername() . ' byl zbaven práv redaktora.');
                redirectTo('/administrace/uzivatele');
            } else {
                $this->addPopup('danger', 'Uživatel se zadaným uživatelským jménem nemůže být zbaven práv redaktora.');
                redirectTo('/administrace/uzivatele');
            }
        }
    }
}