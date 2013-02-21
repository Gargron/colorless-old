<div class="widget-block">
<!--    <div class="widget-ok round-10">
        <h4>Activity <span id="activity-speed"></span></h4>
        <div id="activity-stream" style="overflow:hidden;overflow-y:visible;height:50px;">

        </div>
    </div>-->
    <div class="widget-ok round-10">
        <h4>You are on the old site. New site is here:</h4>
        <p><em>The site has been updated on the 24th December 2011</em>. <strong><a href="http://thecolorless.net">Please go there</a></strong> when you are finished with the archives.</p>
    </div>
    <div class="widget-ok round-10">
        <h4>Some tags</h4>
        <ul class="tags tags-list">
        <?php $tags = $this->chan->getTagsAll(0, 10); ?>
        <?php foreach($tags as $t): ?>
            <li><?php echo anchor('tags/'.$t->tagSlug, '<span>'.$t->tagSlug.'</span><em>'.$t->tagCount.'</em>'); ?></li>
        <?php endforeach; ?>
            <li><?php echo anchor('about/tags', 'More &raquo;'); ?></li>
        </ul>
    </div>
</div>
<div class="widget-block">
    <div class="widget-tabbed">
        <ul class="tab-links">
            <li class="active"><a href="#">Stats</a></li>
            <li><a href="#">Find us</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-is active">
                <ul class="fields-list">
                    <li><span><?php echo number_format($this->cache->model('chan', 'numPosts', array(), 3600)); ?></span> posts</li>
                    <li><span><?php echo number_format($this->cache->model('chan', 'numThreads', array(), 3600)); ?></span> threads</li>
                    <li><span><?php echo number_format($this->cache->model('user', 'numActiveUsers', array(), 3600)); ?></span> users</li>
                </ul>
            </div>
            <div class="tab-is">
                <ul class="fields-list">
                    <li><span>~</span><a href="http://twitter.com/TheColorless" rel="nofollow" class="link-twitter">On Twitter</a></li>
                    <li><span>~</span><a href="http://facebook.com/thecolorless" rel="nofollow" class="link-facebook">On Facebook</a></li>
                    <li><span>~</span><a href="http://steamcommunity.com/groups/Colorless" rel="nofollow" class="link-steam">On Steam</a></li>
                </ul>
            </div>
        </div>
    </div><!--.widget-tabbed-->
</div>
