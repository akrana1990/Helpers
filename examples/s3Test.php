<?php require_once (__DIR__.'/../vendor/autoload.php'); ?>

<?php

use App\Helpers\S3Helper;

$pageTitle = 'Welcome to Simple Storage Service';

ini_set('display_errors',1);
error_reporting(E_ALL);

?>
<?php require_once('layouts/header.php'); ?>

<div class="well">
    <form class="form-inline" action="" method="post">
        <div class="form-group">
            <label>Upload File</label>
            <input class="form-control" type="file" name="s3File">
            <button class="btn btn-default" type="submit" name="submit">Save</button>
        </div>
    </form>
</div>

<table class="table table-stripped">
    <thead>
    <tr>
        <th>File Name</th>
        <th>File URL</th>
        <th>Bucket</th>
        <th>Directory</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    </tbody>
</table>


<?php require_once('layouts/header.php'); ?>

