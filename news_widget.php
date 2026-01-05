<?php
$rss_url = "https://www.rollingstone.com/music/music-news/feed/"; 

$rss = @simplexml_load_file($rss_url);

if ($rss) {
    echo "<ul class='list-unstyled' style='padding-left: 0;'>";
    $count = 0;
    
    foreach ($rss->channel->item as $item) {
        if ($count >= 10) break; //doar primele 10 stiri
        
        echo "<li class='news-item'>";
        echo "<strong><a href='{$item->link}' target='_blank'>{$item->title}</a></strong>";
        //data publicarii
        echo "<br><small>" . date("d M Y", strtotime($item->pubDate)) . "</small>";
        echo "</li>";
        
        $count++;
    }
    echo "</ul>";
} else {
    //mesaj dacă nu merge
    echo "<p class='text-danger'>Nu s-au putut prelua știrile momentan.</p>";
}
?>
