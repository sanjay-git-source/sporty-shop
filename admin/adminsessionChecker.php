<?php
if (!isset($_SESSION['user'])) 
{
    //If you want to use script or header
    echo "<script>window.location.href='sporty_shop@admin.php'</script>"; 
    //header("Location: prof@ilai.html");
    exit();
}
?>