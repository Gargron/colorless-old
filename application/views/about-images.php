<?php $this->load->view('common/header'); ?>
<div class="form-awesome round-10 form-awesome-wider">
        <h3 id="rules">Rules</h3>
        <ol class="non-form">
            <li><p>Do not upload multiple identical files, try to upload only high-quality images without artefacts.</p></li>
            <li><p>For now, only 2D pictures are to be uploaded, screenshots excluded. Official art, artworks, and even own art are allowed if they look good enough</p></li>
            <li><p>Not Safe For Work material is allowed, but must be marked as such on the tagging page.</p></li>
            <li><p>No child pornography, including 2D depictions (hello, Canada). Breaking this rule will make the consequences never be the same.</p></li>
            <li><p>No purely-practical images (comics, manga, signatures, reactionfaces, memes)</p></li>
        </ol>
	<hr />
	<h3 id="tagging">Tagging</h3>
        <h4>Tagging style</h4>
        <p>The tagging system is quite simple, but may take a bit of getting used to. First, for those that do not get it yet:</p>
        <p><em>All multi-word tags are to be connected by an underscore(_)! When you use a space between them, it becomes a new tag!</em></p>
        <p>For example "<code>blue_hair</code>" denotes that a character has blue hair, while "<code>blue hair</code>" denotes they are blue, and have hair. Huge difference. Couple that with the fact that neither blue nor hair are tags on their own, it will never display, and will not be searchable. You're only hurting us.</p>

        <h4>Wait, what? Where do these go?</h4>
        <p>Well, you see, there are four different tag categories, <em>General, Character, Copyright,</em> and <em>Artist</em> tags.</p>

        <h5>General tags</h5>
        <h6>Explanation</h6>
        <p><b>Tag anything that you can <em>see</em>.</b> Slightly more verbose: We are tagging anything visual about the image, not anything that we "feel" from it. </p>

        <h6>Examples:</h6>
        <ul class="bullet">
            <li><p><code>blue_hair</code> : Characters such as Rei, from NGE, whom have blue hair, would get this tag</p></li>
            <li><p><code>gun</code> : Images that depict a character wielding a gun, or have a gun as a prominent part of the picture, would get this tag</p></li>
            <li><p><code>night</code> : A picture in which the time of day is clearly night, and plays into the theme of the picture, ought to be tagged with this</p></li>
        </ul>

        <p>You should attempt to avoid subjective tagging (tagging based upon opinion). Common subjective tags are "sexy", "silly", "moe", "dumb", and "cute". </p>

        <h6>Common things to tag</h6>
        <ul class="bullet">
		<li>
		<p>Characters, and things about them, such as <code>hair/color/length/style/decorations</code>.</p>
		</li>
		<li>
		<p>Objects, such as a <code>weapon</code>, <code>large_breasts</code>, a <code>feather</code>, etc</p>
		</li>
		<li>
		<p>Expressions, such as <code>sad</code>, <code>smile</code>, etc</p>
		</li>
		<li>
		<p>"Disgusting" things, that others may wish to avoid, such as <code>peeing</code>, <code>scat</code>, <code>vomit</code>, <code>guro</code>, <code>yaoi</code>, <code>yuri</code>, <code>rape</code>, <code>spoilers</code>, etc</p>
		</li>
		<li>
		<p>The setting, such as <code>Christmas</code>, <code>night</code>, a <code>battle</code>, or on a <code>train</code>, etc</p>
		</li>
        </ul>

        <p>If you feel that you cannot fill out all of the tags, due to not knowing the names over everything/everyone in the image, tag it with <code>tagme</code>.</p>

        <h5>Character Tags</h5>
        <h6>Explanation</h6>
        <p>Any and all named characters that appear in the image, or are assumed to be in the image, such as those that may be holding the "camera".</p>

        <h6>Tagging style</h6>
        <p>This can be a tad bit iffy, at best. As our tags were ripped from <a href="http://danbooru.donmai.us" rel="nofollow">Danbooru</a>, we must follow their tagging style for character names. The only problem with this is that not everything is tagged the same way.</p>

        <p><strong>From their guidelines on character tagging:</strong></p>

        <blockquote>
		<p>
		This is somewhat complicated. In general, use whatever order the the anime uses. Failing this, use the ordering the character's nationality suggests. This typically means LastName FirstName order for Asian names, and FirstName LastName order for Western names.
		</p>
		<p>But there are exceptions. Some characters use FirstName LastName order despite having Asian-sounding names. Subaru Nakajima is a good example of this (in all official promotional artwork FirstName LastName order is used). There is nothing we can do but shake our heads.
		</p>
		<p>Some characters have a mixture of Asian and Western names. Refer to the source material for these cases. Failing that, the general rule is, use whatever ordering the character's last name suggests. Asuka Langley Soryuu has a Japanese last name, so it would become <code>soryuu_asuka_langley</code>. Akira Ferrari has an Italian last name, so it becomes <code>akira_ferrari</code>. But again, there are exceptions to this like <code>setsuna_f_seiei</code>.
		</p>
		<p><b>Use full names</b></p>
		<p>
		Using full names reduces the chances of collisions. The definitive resource for character names is <a href="http://www.animenewsnetwork.com/">Anime News Network</a> (note that all their character names use FirstName LastName order).
		</p>
        </blockquote>

        <p>What I can suggest is putting both character orders in the Characters field, and the non-existent tag will be pruned. If you want to be more hands-on, you can  always look up the tag on Danbooru, to see which one is in use on there.</p>

        <h5>Copyright Tags</h5>
        <h6>Explanation</h6>
        <p>These tags are for the names of the copyrighted works from which the characters are derived, whether they be a game, book, anime, manga, or what-have-you.</p>

        <h6>Tagging style</h6>
        <p>Again, there is room for confusion. As shows can have multiple titles, thanks to being licensed by multiple groups in multiple countries, it can be a bit of a grey area. Add in the fact that Danbooru doesn't have one set style for such things, and you have a recipe for disaster. As such, I feel that it is more fitting to use another quote from their guide.</p>
        <blockquote>
		<p>Please use Danbooru's existing tags. Danbooru may use translated or native titles when naming copyrights and characters, so always double check.</p>
		<p>Use of Translated and Native titles for Copyrights is quite unpredictable on Danbooru. Below are examples of the unpredictable use of Native and English translated titles for copyright tags on Danbooru. Note that Danbooru was founded in 2005, according to the age of post #1. Compare this to the initial English release of the following internationally licensed copyrights.</p>
		<ul>
			<li>
				<p>Original Title Used:</p>

				<ul class="bullet">
					<li>
						<code>Bishoujo_Senshi_Sailor_Moon</code>, not Pretty Soldier Sailor Moon
					</li>
					<li>
						<code>Higurashi_ni_Naku_Koro_Ni</code>, not Higurashi When They Cry
					</li>
					<li>
						<code>Hayate_no_Gotoku!</code>, not Hayate the Combat Butler
					</li>
				</ul>
			</li>
			<li>
				<p>English Title Used:</p>

				<ul class="bullet">
					<li>
						<code>Neon_Genesis_Evangelion</code>, not Shin Seiki Evangelion
					</li>
					<li>
						<code>Pokemon</code>, not Pocket Monsters
					</li>
					<li>
						<code>Vampire_Princess_Miyu</code>, not Kyuuketsuki Miyu
					</li>
				</ul>
			</li>
			<li>
				<p>Native English Subtitle Used:</p>

				<ul class="bullet">
					<li>
						<code>Imperishable_Night</code>, not Touhou Eiyashou
					</li>
					<li>
						<code>Mountain of Faith</code>, not Touhou Fuujinroku
					</li>
					<li>
						Exception: <code>Touhou_Hisoutensoku</code>, due to lack of an official English subtitle.
					</li>
				</ul>
			</li>
		</ul>
        </blockquote>
        <p>If you are having trouble, again, it is a good idea to put in multiple titles for each image, until you are sure which tags are in use, and which are not. An even better way of doing things is to check the tags against Danbooru, using <a href="http://www.animenewsnetwork.com/">ANN</a> as a resource for the different titles.</p>

        <h5>Artist Tags</h5>
        <h6>Explanation</h6>
        <p>Basically, whatever artists had their hands in  making the picture. You needn't include the artist from which a derivative work is based, such as tagging <code>kubo_tite</code> for every piece of <code>Bleach</code> fanart.</p>

        <h6>How to find the artist</h6>
        <p>The best way that I have found is to run the images through <a href="http://iqdb.org/">IQDB</a>, and find a link to Danbooru or Gelbooru. When you do, copy the artist tag from there, and throw it in. This can also be hand for finding tags that you may not have thought of, otherwise.</p>

        <p>If this is too hard, <a href="http://thecolorless.net/direct/compose/5753">drop a line</a> by <a href="http://www.thecolorless.net/user/acostoss">acostoss</a>, and he'll be glad to help you out with it.</p>
	<hr />
	<h3 id="filtering">Filtering</h3>
            <p>Coming soon.</p>
</div>
<?php $this->load->view('common/footer'); ?>