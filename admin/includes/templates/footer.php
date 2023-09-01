<?php
  if(isset($needsFooter)){
    ?>
<footer class='site-footer'>
  <div class="mycontainer">
    <div class="platform-logo-links">
      <a href="<?=ROOTPATH?>index.php" class="logo">ReStO</a>
      <div class="icons between-flex mt-20">
        <a class="fb" href="#"><i class="fab fa-facebook-f fa-fw"></i></a>
        <a class="gl" href="#"><i class="fab fa-google fa-fw"></i></a>
        <a class="tw" href="#"><i class="fab fa-twitter fa-fw"></i></a>
      </div>
    </div>

    <div class="foot-col">
      <h2>التواصل</h2>
      <div class="foot-contact">
        <i class="fas fa-location-dot"></i> Resto, حلب الاسماعيلية
      </div>
      <div class="foot-contact">
        <i class="fas fa-phone"></i> +963-912345678
      </div>
      <div class="foot-contact">
        <i class="fas fa-envelope"></i> restoplatform0@gmail.com
      </div>
    </div>

    <div class="foot-col">
      <h2>حول المنصة</h2>
      <a href="#" class="foot-contact">من نحن</a>
      <a href="#" class="foot-contact">فريقنا</a>
      <a href="#" class="foot-contact">سياسة الخصوصية</a>
    </div>
  </div>
</footer>

<?php
  }
?>
<!-- Scripts -->
<script src=<?= $js . "bootstrap.min.js"?>></script>
<script src=<?= $js . "jQuery.js"?>></script>
<!-- jquery-ui (Needed for SelectBoxIt plugin) -->
<script src=<?= $js . "jquery-ui.min.js"?>></script>
<script src=<?= $js . "validate.js"?>></script>
<script src=<?= $js . "my-validator.js"?>></script>


<script src=<?= $js . "slick.min.js"?>></script>
<!-- SelectBoxIt plugin -->
<script src=<?= $js . "jquery.selectBoxIt.min.js"?>></script>

</script>
<?php
  if(isset($jsFiles)){
    foreach($jsFiles as $file){
      echo "<script src='$file'></script>";
    }
  }
  ?>
<!-- Main Script File -->
<script src=<?= $js . "backend.js"?>></script>
</body>

</html>