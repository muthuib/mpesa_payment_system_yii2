<?php
/**
 * This is the footer page
 */
?>
<footer id="footer" class="footer">
    <div class="copyright">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; <strong><span><?php echo Yii::$app->name ?></strong><span> <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end">All Rights Reserved</div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>