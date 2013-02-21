<?php
    $tagsarr = array();
    foreach($tags as $tag)
            $tagsarr[] = $tag->tagName;

    $tags_f = implode(" ", $tagsarr);

    $dim = explode("x", $dimensions);

$chr = array();
$gen = array();
$cop = array();
$art = array();

$source_url = $source;
$source = str_replace('http://www.', '', $source);
$source = str_replace('http://', '', $source);
if (strlen($source) > 25) {
  $source = substr($source, 0, 13) . ".." . substr($source, -4);
}

foreach($tags as $t) {
    switch($t->tagModel) {
        case "character":
            $chr[] = $t;
            break;
        case "artist":
            $art[] = $t;
            break;
        case "copyright":
            $cop[] = $t;
            break;
        default:
            $gen[] = $t;
            break;
    }
}

    echo '<!--' . $original_post . '-->';
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $tags_f; ?> - The Colorless</title>
    <link rel="stylesheet" type="text/css" href="/min/b=css&amp;f=reset.css,minimalistic.css,jquery.autocomplete.css" />
    <meta name="keywords" content="<?php echo $tags_f; ?>" />
</head>
<body class="image-view">
<!-- BuySellAds.com Ad Code -->
    <script type="text/javascript">
    (function(){
      var bsa = document.createElement('script');
         bsa.type = 'text/javascript';
         bsa.async = true;
         bsa.src = '//s3.buysellads.com/ac/bsa.js';
      (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);
    })();
    </script>
    <!-- End BuySellAds.com Ad Code -->
    <div id="image-wrapper">
        <div id="image-wrapper-overlay">
            <div id="image-info">
                <h1><a href="http://thecolorless.net/i" rel="home"><span></span><em>The</em> <strong>Color</strong>less</a></h1>
                <hr />
                <h4><label for="searchq">Search</label></h4>
                <form action="/i" method="get" id="searchf">
                <input type="text" name="q" id="searchq" size="29" />
                </form>
                <?php if(!empty($original_post)): ?>
                <a href="<?php echo $original_post; ?>" id="image-info-origin">View original post</a>
                <?php endif; ?>
                <h4>Info</h4>
                    <ul>
                        <li><span class="tis-title">Uploader:</span> <?php echo anchor('i/?q=user:'.$user->userName, $user->userName); ?></li>
                        <li><span class="tis-title">Posted:</span> <?php $ago = timespan(human_to_unix($timestamp)); $ago = explode(',', $ago); echo $ago[0]; ?> ago</li>
                        <li><span class="tis-title">Dimensions:</span> <?php echo $dimensions; ?></li>
                        <li><span class="tis-title">Size:</span> <?php echo $size; ?></li>
                    </ul>
                <hr />
                    <!-- BuySellAds.com Zone Code -->
                    <div id="bsap_1257678" class="bsarocks bsap_6185bec2da7bb340a6a5e59e4e0e5995"></div>
                    <!-- End BuySellAds.com Zone Code -->
                <hr />
                <h4>Details
                  <?php if ($this->user->isMod() || $this->user->isAdmin() || $this->user->id == $user->userID) { ?>
                    - <?php echo anchor('/image/delete/'.$filename, 'Delete'); ?>
                  <?php } ?>
                </h4>
                    <ul>
                        <?php if(!empty($chr)): ?>
                        <li><span class="tis-title">Characters:</span>
                          <ul class="tags characters">
                            <?php foreach($chr as $tag) { ?>
                                <li>
                                  <?php echo anchor('/i/?q='.$tag->tagName, str_replace('_', ' ', $tag->tagName) . ' (' . $tag->tagUploadCount . ')'); ?>
                                </li>
                            <?php } ?>
                          </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(!empty($art)): ?>
                        <li><span class="tis-title">Artist:</span>
                          <ul class="tags artist">
                            <?php foreach($art as $tag) { ?>
                                <li>
                                  <?php echo anchor('/i/?q='.$tag->tagName, str_replace('_', ' ', $tag->tagName) . ' (' . $tag->tagUploadCount . ')'); ?>
                                </li>
                              <?php } ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(!empty($cop)): ?>
                        <li><span class="tis-title">Copyright:</span>
                          <ul class="tags copyright">
                            <?php foreach($cop as $tag) { ?>
                                <li>
                                  <?php echo anchor('/i/?q='.$tag->tagName, str_replace('_', ' ', $tag->tagName) . ' (' . $tag->tagUploadCount . ')'); ?>
                                </li>
                            <?php }?>
                          </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(!empty($gen)): ?>
                        <li><span class="tis-title">Tags:</span>
                          <ul class="tags general">
                            <?php foreach($gen as $tag) { ?>
                              <li>
                                <?php echo anchor('/i/?q='.$tag->tagName, str_replace('_', ' ', $tag->tagName) . ' (' . $tag->tagUploadCount . ')'); ?>
                              </li>
                            <?php } ?>
                          </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <!--<ul>
                      <li>
                        <a href="http://anime.jlist.com/click/3341/118" target="_blank"  title="You've got a friend in Japan at J-List!">
                          <img src="http://anime.jlist.com/media/3341/118" width="160" height="180" style="display:block;margin:0 auto" alt="You've got a friend in Japan at J-List!" border="0">
                        </a>
                      </li>
                    </ul>-->
            </div><!--#image-info-->
        </div>
        <a href="<?php echo $original_source; ?>">
            <img id="mainimg" src="<?php echo $medium_source; ?>" alt="<?php echo $tags_f; ?>" />
        </a>
    </div>
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-7104252-6']);
      _gaq.push(['_trackPageview']);
      _gaq.push(['_setCampMediumKey', 'ref']);
      _gaq.push(['_setCampSourceKey', 'ref']);
      _gaq.push(['_setCampNameKey', 'type']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>
</body>
</html>