<?php
require_once ('parser.php');
$parser = new \IcmUtils\Parser1c('/wp-content/icmark/upload_from_1c', true);
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
}
?>
<script>
    alert(123);
</script>
<?php
die();

