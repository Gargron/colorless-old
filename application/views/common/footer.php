                </div><!--#content-->
                <div id="primary-aside">
                    <?php $this->load->view('common/sidebar'); ?>
                </div><!--#primary-aside-->
            </div><!--#container-->
            <div id="footer">
                <div id="site-info" class="right">
                    <?php echo anchor('', '<span>Colorless</span>'); ?>
                </div><!--#site-info-->
                <p><a href="http://thecolorless.net">The Colorless</a> (thecolorless.net) created by Eugen R. and Ryan A.
                <br />All trademarks and copyrights on this site are owned by their respective parties. Posts are owned by their authors, with all the responsibility.</p>
            </div><!--#footer-->
	</div><!--#wrapper-->
	<a href="#wrapper" id="back-to-top-master" class="tooltipped-b round-10" title="Back to top">&uarr;</a>
    <canvas id="snowfield" style="display:none"></canvas>
    <script type="text/javascript">
    <?php if($threadID && $this->uri->segment(1) == 'thread' && $is_last): ?>
    var threadID = <?php echo $threadID; ?>;
    <?php endif; ?>
    var live     = false;
    </script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="/min/b=js&amp;f=ajaxupload.js,jquery.tipTip.minified.js,jquery.tagsinput.js,jquery.scrollTo-min.js,jquery.maskedinput-1.2.2.js,jquery.reveal.js,functions.js,plugins/jquery.color.js,snowfall.js"></script>
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
<!--Page generated in: <?php echo $this->benchmark->elapsed_time();?>-->
