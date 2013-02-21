<?php

class About extends Controller {
    function About() {
        parent::Controller();
    }

    function index() {
        $data['page_title'] = "About us";
        $this->load->view('about', $data);
    }

    function tags() {
        $tags = $this->chan->getTagsAll(0, 300);
        $data['tags'] = $tags;
        $data['page_title'] = "300 tags";
        $this->load->view('about-tags', $data);
    }

    function rules() {
        $data['page_title'] = "Rules";
        $this->load->view('about-rules', $data);
    }

    function advertising() {
        $data['page_title'] = "Ads";
        $this->load->view('about-ads', $data);
    }

    function appeal() {
        echo '<p style="display:block;width:610px;margin:20px auto;text-align:center"><img src="http://i.imgur.com/1kfe6.jpg" /><br />Become my magical girl.</p>';
        echo '<p style="display:block;width:610px;margin:20px auto;text-align:center;color: #dedede">Okay, but seriously, guys, my appeal is: Be kind to each other. Use all the functions we offer. And <a href="http://twitter.com/TheColorless" rel="nofollow" style="color:inherit">follow us on Twitter</a> and <a href="http://facebook.com/thecolorless" rel="nofollow" style="color:inherit">like us on Facebook</a> so you\'d get our announcements. Also link to us from your blogs/websites so we\'d get some exposure outside. We need that ;w;<br /><right>&mdash;Gargron</right><br /><br />Also yeah, I totally felt like lulz.</p>';
    }

    function faq() {
        $data['page_title'] = "Frequently Asked Questions";
        $this->load->view('about-faq', $data);
    }

    function sitemap() {
        header('Content-type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
      <loc>http://thecolorless.net/</loc>
      <changefreq>always</changefreq>
      <priority>0.8</priority>
   </url>
   <url>
      <loc>http://thecolorless.net/board/random</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>
   <url>
      <loc>http://thecolorless.net/board/anime</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>
    <url>
      <loc>http://thecolorless.net/board/life</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>
   <url>
      <loc>http://thecolorless.net/board/projects</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>
   <url>
      <loc>http://thecolorless.net/board/games</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>
   <url>
      <loc>http://thecolorless.net/board/love</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>
   <url>
      <loc>http://thecolorless.net/board/design</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>
   <url>
      <loc>http://thecolorless.net/board/staff</loc>
      <changefreq>always</changefreq>
      <priority>0.5</priority>
   </url>'."\n";
        //$threads = $this->datastore->store('all', array('chan', 'retrieveThreads'), array(), 'sitemap', 3600*6);
        $threads = $this->chan->retrieveThreads();
        foreach($threads as $t) {
            $t = (array) $t;
            echo '<url>
      <loc>http://thecolorless.net/thread/'.$t["threadID"].'</loc>
      <changefreq>daily</changefreq>
      <priority>0.7</priority>
    </url>'."\n";
        }
        echo '</urlset>';
    }

    function banned() {
        $this->load->view('banned');
    }

}
?>