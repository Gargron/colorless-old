<?php

class Feed extends Controller {
    function Feed() {
        parent::Controller();

        $this->load->helper('xml');
        $this->load->helper('text');
    }

    function user($name) {
        $user = $this->user->getUsers(array('userName' => $name));
        $threads = $this->chan->retrieveThreads(array('threadCreatorID'=>$user->userID, 'sortBy'=>'threadCreatedAt', 'sortDirection'=>'desc', 'limit' => 10));

        if(empty($threads)) {
            header("HTTP/1.1 204 No Content");
            die("Empty like a blank sheet of paper.");
        }

        $name = $user->userName;
        $link = site_url('feed/user/'.$user->userName);
        $pubDate = date('r', human_to_unix($threads[0]->threadCreatedAt));

        header('Content-type: application/rss+xml');
        echo <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>{$name} / The Colorless</title>
        <link>{$link}</link>
        <pubDate>{$pubDate}</pubDate>
        <language>en</language>
        <description>Threads on TheColorless created by {$name}</description>
XML;
        foreach($threads as $t) {
            $title = xml_convert($t->threadTitle);
            $link = site_url('thread/'.$t->threadID);
            $name = $user->userName;
            $pubDate = date('r', human_to_unix($t->threadCreatedAt));
            $content = $this->chan->format($t->threadOP);
            $description = xml_convert($t->threadOP);
            echo <<<XML
        <item>
            <title>{$title}</title>
            <link>{$link}</link>
            <dc:creator>{$name}</dc:creator>
            <guid isPermaLink="false">{$link}</guid>
            <pubDate>{$pubDate}</pubDate>
            <description><![CDATA[{$description}]]></description>
            <content:encoded><![CDATA[{$content}]]></content:encoded>
        </item>
XML;
        }
        echo <<<XML
    </channel>
</rss>
XML;
    }

    function board($name) {
        $threads = $this->chan->retrieveThreads(array('threadBoard' => $this->chan->slugBoard($name), 'sortBy'=>'threadID', 'sortDirection'=>'desc', 'limit' => 10));

        if(empty($threads)) {
            header("HTTP/1.1 204 No Content");
            die("Empty like a blank sheet of paper.");
        }

        $title = ucwords($name) . " / The Colorless";
        $link = site_url('feed/board/'.$name);
        $pubDate = date('r', human_to_unix($threads[0]->threadCreatedAt));

        header('Content-type: application/rss+xml');
        echo <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>{$title}</title>
        <link>{$link}</link>
        <pubDate>{$pubDate}</pubDate>
        <language>en</language>
        <description>Threads on TheColorless in the board {$name}</description>
XML;
        foreach($threads as $t) {
            $title = xml_convert($t->threadTitle);
            $link = site_url('thread/'.$t->threadID);
            $name = $t->threadCreatorUserName;
            $pubDate = date('r', human_to_unix($t->threadCreatedAt));
            $content = $this->chan->format($t->threadOP);
            $description = xml_convert($t->threadOP);
            echo <<<XML
        <item>
            <title>{$title}</title>
            <link>{$link}</link>
            <dc:creator>{$name}</dc:creator>
            <guid isPermaLink="false">{$link}</guid>
            <pubDate>{$pubDate}</pubDate>
            <description><![CDATA[{$description}]]></description>
            <content:encoded><![CDATA[{$content}]]></content:encoded>
        </item>
XML;
        }
        echo <<<XML
    </channel>
</rss>
XML;
    }

    function thread($id) {
        $thread = $this->chan->retrievePosts(array('postID' => $id));
        $posts = $this->chan->retrievePosts(array('postParentID' => $id, 'sortBy'=>'postID', 'sortDirection'=>'desc', 'limit' => 10));

        if(empty($posts)) {
            header("HTTP/1.1 204 No Content");
            die("Empty like a blank sheet of paper.");
        }

        $name = xml_convert($thread->postTitle);
        $link = site_url('feed/thread/'.$id);
        $pubDate = date('r', human_to_unix($posts[0]->postCreatedAt));

        header('Content-type: application/rss+xml');
        echo <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <channel>
        <title>{$name} / The Colorless</title>
        <link>{$link}</link>
        <pubDate>{$pubDate}</pubDate>
        <language>en</language>
        <description>Latest posts in the "{$name}" thread on TheColorless</description>
XML;
        foreach($posts as $p) {
            $title = "#{$p->postID} / {$p->userName}";
            $link = site_url('thread/gotopost/'.$thread->postID.'/'.$p->postID);
            $name = $p->userName;
            $pubDate = date('r', human_to_unix($p->postCreatedAt));
            $content = $this->chan->format($p->postContent);
            $description = xml_convert($p->postContent);
            echo <<<XML
        <item>
            <title>{$title}</title>
            <link>{$link}</link>
            <dc:creator>{$name}</dc:creator>
            <guid isPermaLink="false">{$link}</guid>
            <pubDate>{$pubDate}</pubDate>
            <description><![CDATA[{$description}]]></description>
            <content:encoded><![CDATA[{$content}]]></content:encoded>
        </item>
XML;
        }
        echo <<<XML
    </channel>
</rss>
XML;
    }
}