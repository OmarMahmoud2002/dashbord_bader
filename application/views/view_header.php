<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$error_message = '';
$success_message = '';
$header_CI =& get_instance();
$is_admin_shell = ($header_CI->uri->segment(1) === 'admin');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      
    <meta name="description" content="">
    <meta name="keywords" content="">
    
    <link rel="icon" type="image/png" href="">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/animate.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/toastr.min.css">
    <link href="https://unpkg.com/slim-select@latest/dist/slimselect.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo base_url(); ?>public/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/main.css?a=<?php $date=date_create();echo date_timestamp_get($date);?>">
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/login.css?a=<?php $date=date_create();echo date_timestamp_get($date);?>">
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/search_product.css?a=<?php $date=date_create();echo date_timestamp_get($date);?>">
    <?php if ($is_admin_shell) { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/admin-ui-pattern.css?a=<?php $date=date_create();echo date_timestamp_get($date);?>">
    <?php } ?>


</head>
<body<?php echo $is_admin_shell ? ' class="admin-ui-shell"' : ''; ?>>
