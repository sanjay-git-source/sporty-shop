<?php
session_start();
?> 
<?php
session_unset();
session_destroy();
echo "<script>window.location.href='sporty_shop@admin.php'</script>";
exit();
?>