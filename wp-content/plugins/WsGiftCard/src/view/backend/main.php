<?php
/**
 * @var 
 */
?>
<form action="<?= admin_url('admin.php?page=ws-gift-cards') ?>" method="post">
    <input type="email" name="email">
    <input type="text" name="date">
    <input type="text" name="message">
    <input type="text" name="product_name">
    <input type="text" name="product_price">
    <input type="text" name="product_image">
    <input type="text" name="barcode">
    <input type="text" name="pin">
    <input type="submit" name="submit" value="save">
</form>

<form action="<?= admin_url('admin.php?page=ws-gift-cards') ?>" method="post">
    <input type="hidden" name="id" value="1">
    <input type="submit" name="delete" value="delete">
</form>
