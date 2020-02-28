</div><!-- END #main -->

<?php if ($showNav) { ?>

<div id="nav">
<ul>
<li><a href="index.php" class="dashboard">Dashboard</a></li>
<li><a href="blog.php" class="entries">Entries</a></li>
<li><a href="comment.php" class="comments">Comments</a></li>
<li><a href="category.php" class="categories">Categories</a></li>
<li><a href="options.php" class="options">Options</a></li>
<li><a href="index.php?action=logout">Logout</a></li>
</ul>
</div>

<?php } ?>

<div class="credit">
<p>Powered by <a href="<?php echo $this->getOpt('url'); ?>">FanUpdate <?php echo $this->getOpt('version'); ?></a></p>
</div>

</div><!-- END #wrap -->

</body>

</html>