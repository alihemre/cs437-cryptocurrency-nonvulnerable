<!-- public/article.php -->
<?php
$title = "Article";
include './header.php'; 

// In reality, you'd fetch article data from a database using $_GET['id']
// For demonstration, let's just mock something
$articleId = isset($_GET['id']) ? $_GET['id'] : 0;
$mockArticles = [
    1 => ["title" => "Bitcoin Hits $50K", "content" => "Full article text about BTC..."],
    2 => ["title" => "Ethereum Merge Update", "content" => "Full article text about ETH Merge..."],
    3 => ["title" => "Altcoin Rally", "content" => "Full article text about altcoin rally..."]
];

if (array_key_exists($articleId, $mockArticles)) {
    $articleTitle = $mockArticles[$articleId]["title"];
    $articleContent = $mockArticles[$articleId]["content"];
} else {
    $articleTitle = "Article Not Found";
    $articleContent = "Sorry, we couldn't find this article.";
}
?>

<main>
  <h2><?php echo $articleTitle; ?></h2>
  <p><?php echo $articleContent; ?></p>
  <a href="news.php">&larr; Back to News</a>
</main>

<?php include './footer.php'; ?>
