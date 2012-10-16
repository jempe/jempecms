<?php 
$this->output->set_header("HTTP/1.1 200 OK");
$this->output->set_header("Content-Type: application/xml; charset=utf-8");

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";


 ?>

  <urlset xmlns="http://www.google.com/schemas/sitemap/0.84">
<?php foreach($pages as $page){ ?>
   <url>
    <loc><?=$page["link"] ?></loc>
    <lastmod><?=$page["modified"] ?></lastmod>
   </url>  
<?php } ?>
  </urlset>