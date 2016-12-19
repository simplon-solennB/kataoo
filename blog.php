<?php

class BlogJsonLoader implements IBlogLoader
{
    /**
     * @param String $path
     * @return array
     */
    public function load(String $path): array
    {
        $rawData = file_get_contents($path);
        return $this->parse($rawData);
    }

    /**
     * parse les données JSON et renvoie une liste d'articles
     * @param String $rawData donnees json_decodées
     * @return array
     */
    public function parse(String $rawData):array{
        $rawAuthors = $rawData['authors'];
        $authors = array_map(function ($rawAuthor) {
            return new Author($rawAuthor['id'], $rawAuthor['firstname'], $rawAuthor['lastname']);
        }, $rawAuthors);

        $rawArticles = $rawData['articles'];

        // pour chaque rawArticle on veut récup une instance de Article
        $articles = array_map(function ($rawArticle) use($authors){

            $articleAuthorId = $rawArticle["authorId"];

            $articleAuthors = array_filter(
                $authors,function($author) use($articleAuthorId){
                return $author->id == $articleAuthorId;
            });

            $articleAuthor = current($articleAuthors);

            return new Article(
                $rawArticle["id"], $rawArticle["title"],
                $rawArticle["content"], $articleAuthor,
                new DateTime($rawArticle['date'])
            );
        }, $rawArticles);
        return $articles;
    }
}



/**
 * Class BlogCSVLoader charge les articles depuis fichier csv
 * id / title / content / date / authorId / firstname / lastname
 */
class BlogCSVLoader extends BlogJsonLoader{

}

/**
 * Class BlogDBLoader charge les articles depuis une base de données
 */
class BlogDBLoader implements IBlogLoader{

    /**
     * @param $dbname
     */
    function load(String $path):array
    {
        // TODO: Implement load() method.
    }
}

interface IBlogLoader{
    /**
     * @param String $path
     * @return array Article
     */
    function load(String $path):array;
}

/**
 * Class Autor
 * description d'un rédacteur
 */
class Author
{
    public $id;
    public $firstName;
    public $lastName;

    public function __construct(int $id, String $firstName, String $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * renvoie le nom complet : Bob Lee
     * @return String
     */
    function getName(): String
    {
        return $this->firstName. "." .$this->lastName ;
    }

    /**
     * renvoie le initial du prénom et nom complet : B.Lee
     * @return String
     */
    function getShortName(): String
    {
        return strtoupper($this->firstName[0]). "." .$this->lastName;
    }

    /**
     * renvoie les initiales : B.L
     * @return String
     */
    function getInitial(): String
    {
        return strtoupper( $this->firstName[0]. "." .$this->lastName[0] );
    }
}

/**
 * Class Article
 */
class Article
{
    public $id;
    public $title;
    public $content;
    public $author;
    public $publicationDate;

    public function __construct(int $id, String $title, String $content, Author $author, DateTime $date)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->publicationDate = $date;
        $this->author = $author;
    }
}

class ArticleRenderer
{

    private $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * renvoie l'article mis en forme
     * <h2>{% $title %}</h2>
     * <p>article</p>
     * <p>par nom-court, le date </p>
     * @return String
     */
    function render(): String
    {
        return "<h2>".$this->article->title."</h2><p>"
            .$this->article->content."</p><p>"
            .$this->article->author->getShortName()
            .", le ".$this->article->publicationDate->format('d-m-Y')." </p>";
    }
}

class Blog
{
    public $title;
    public $articles;

    /**
     * Blog constructor.
     * @param String $title
     * @param array $articles tableau d'instances Articles
     */
    public function __construct(String $title, array $articles)
    {
        $this->title = $title;
        $this->articles = $articles;
    }

    /**
     * Renvoie le header du blog
     * <header><h1>titre</h1></header>
     * @return String
     */
    function displayHeader(): String
    {
        return "<h1>$this->title</h1>";
    }

    /**
     * affiche la liste des titres d'articles sous formes de liens vers les articles
     */
    public function displayArticleList(): String
    {
        // <a href="SELF?articleId=Y">article->title</a>

        $articleLinks = array_map(function($article){
            return "<a href=\"".$_SERVER['PHP_SELF']."?articleId="
                .$article->id."\">$article->title</a>";
        }, $this->articles);

        return join("<hr/>", $articleLinks);
    }

    /**
     * renvoie le contenu HTML d'un article
     * @param int $articleId
     * @return String
     */
    public function displayArticle(int $articleId): String
    {
        $selectedArticle = current( array_filter( $this->articles,
            function($article) use($articleId) {
                return $article->id == $articleId;
            }));
        $renderer = new ArticleRenderer($selectedArticle);

        return $renderer->render();
    }

    /**
     * renvoie un footer avec la date du jour
     * <footer></footer>
     */
    function displayFooter()
    {
        $date = new DateTime();
        return ViewHelper::footer($date->format('d-m-y'));
        //return "<footer>".   ."</footer>";
    }
}

// et pourquoi pas essayer de trouver 2/3 trucs à mettre dans un "helper"
class ViewHelper
{
    private $defaultClass;

    /* static*/ const FOOTER = "footer";

    static $footer_var = "footer";

    public function __construct($defaultClass)
    {
        $this->defaultClass = $this->defaultClass;
    }

    static public function p($text){
        return '<p>'.$text.'</p>';
    }

    static public function footer($text){
        return "<".ViewHelper::FOOTER." ><h3>".$text."</h3></>";
    }
}


$loader = new BlogJsonLoader();
// ou $loader = new BlogCSVLoader();
// ou $loader = new BlogDBLoader();

$articles = $loader->load('blog.json');

$blog = new Blog('Vive la POO', $articles);
//echo $blog->displayHeader();
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $blog->title ?></title>
</head>
<body>
<?= $blog->displayHeader(); ?>
<?= isset($_GET['articleId']) ? $blog->displayArticle($_GET['articleId']) : $blog->displayArticleList(); ?>

<?= $blog->displayFooter(); ?>
</body>
</html>
