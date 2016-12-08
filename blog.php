<?php

class BlogLoader
{
    function load(String $path): array
    {

    }
}

/**
 * Class Autor
 * description d'un rédacteur
 */
class Autor
{
    public $id;
    public $firstName;
    public $LastName;

    public function __construct(int $id, String $firstName, String $lastName)
    {

    }

    /**
     * renvoie le nom complet : Bob Lee
     * @return String
     */
    function getName(): String
    {

    }

    /**
     * renvoie le initial du prénom et nom complet : B.Lee
     * @return String
     */
    function getShortName(): String
    {

    }

    /**
     * renvoie les initiales : B.L
     * @return String
     */
    function getInitial(): String
    {

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
    public $autor;
    public $publicationDate;

    public function __construct(String $title, String $content, Autor $autor, DateTime $date)
    {

    }
}


class ArticleRenderer
{

    public function __construct(Article $article)
    {

    }

    /**
     * renvoie l'article mis en forme
     * <h2>titre</h2>
     * <p>article</p>
     * <p>par nom-court, le date </p>
     * @return String
     */
    function render(): String
    {

    }
}

class Blog
{

    public function __construct(String $title, array $articles)
    {
    }

    /**
     * Renvoie le header  du blog
     * <header>titre
     * @return String
     */
    function displayHeader(): String
    {

    }

    /**
     * affiche la liste des titres d'articles sous formes de liens vers les articles
     */
    function displayArticleList(): String
    {

    }

    /**
     * renvoie le contenu HTML d'un article
     * @param int $articleId
     * @return String
     */
    function displayArticle(int $articleId): String
    {

    }

    /**
     * renvoie un footer avec la date du jour
     * <footer></footer>
     */
    function displayFooter()
    {

    }
}

// et pourquoi pas essayer de trouver 2/3 trucs à mettre dans un "helper"
class ViewHelper
{

}

$articles = new BlogLoader('blog.json');
$blog = new Blog('Vive la POO', $articles);

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
<?= isset($_GET['articleId']) ? $blog->displayArticleList() : $blog->displayArticle($_GET['articleId']); ?>
<?= $blog->displayFooter(); ?>
</body>
</html>
