<!DOCTYPE html>
<html lang="ar">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Render All Elements Normally -->
  <link rel="stylesheet" href=<?= $css . "normalize.css"?> />
  <!-- Font Awesome -->
  <link rel="stylesheet" href=<?= $css . "all.min.css"?>>
  <!-- jQuery UI css (Needed for SelectBoxIt plugin)-->
  <link rel="stylesheet" href=<?= $css . "jquery-ui.css"?>>

  <!-- SelectBoxIt Plugin  -->
  <link rel="stylesheet" href=<?= $css . "jquery.selectBoxIt.css"?>>
  <!-- Bootstrap -->
  <link rel="stylesheet" href=<?= $css . "bootstrap.min.css"?>>
  <!-- Sliders Plugin -->
  <link rel="stylesheet" href=<?= $css . "slick.css"?> />
  <link rel="stylesheet" href=<?= $css . "slick-theme.css"?> />
  <!-- My Framwork -->
  <link rel="stylesheet" href=<?= $css . "styles/framework.css"?>>
  <!-- Main Style File -->
  <link rel="stylesheet" href=<?= $css . "styles/style.css"?>>
  <?php
    if(isset($cssFiles)){
      foreach($cssFiles as $file){
        $path = $css . "styles/" . $file;
        echo "<link rel='stylesheet' href=$path></link>";
      }
    }
    ?>

  <title><?=getTitle()?></title>
  <!-- add root path var and -->
  <!-- session vars to js -->
  <script type="text/javascript">
  const $_ROOT_PATH = "<?=ROOTPATH?>";
  <?php
    if(isset($_SESSION['user'])){
    ?>
  const $_USER_SESSION = JSON.parse('<?=json_encode($_SESSION['user'])?>');
  const $_USER_ID = '<?=$_SESSION['user']['id']?>';
  <?php
    }else{
    ?>
  const $_USER_SESSION = null;
  const $_USER_ID = null;
  <?php }
    ?>
  </script>
</head>

<body>