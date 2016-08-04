<?php require_once (__DIR__.'/../vendor/autoload.php'); ?>

<?php

use App\Helpers\SPHelper;

$pageTitle = 'Welcome to Spark Post';

ini_set('display_errors',1);
error_reporting(E_ALL);


?>
<?php require_once('layouts/header.php'); ?>

<?php

if(!empty( $_POST['sendEmail'] ) && $_POST['sendEmail'] == 'Send'){
    $pdfContent=base64_encode(file_get_contents('http://totalreg-new.dev/admin/documents/8oggvewhh_zis.pdf'));


    $recipients[0]['address']['name'] = 'Ankit';
    $recipients[0]['address']['email'] = $_POST['email'];


    /*$recipients[1]['address']['name'] = 'Ankit';
    $recipients[1]['address']['email'] = 'alex.ankit+12@ithands.net';*/


    $attachments[0]['type'] = 'application/pdf';
    $attachments[0]['name'] = 'document.pdf';
    $attachments[0]['data'] = $pdfContent;

    $substitutionData = [
        'NAME' => 'Clark Griswold',
        'ADDRESS' => '12 Enclave'
    ];


    $options = [
        'content' => [
            'from' => [
                'name' => 'Testing',
                'email' => 'from@sparkpostbox.com',
            ],
            'subject' => 'First Mailing From PHP',
            'html' => '<html><body><h1>Congratulations, {{NAME}}!,Street: {{ADDRESS}}</h1><p>You just sent your very first mailing!</p></body></html>',
            'text' => 'Congratulations, {{name}}!! 1You just sent your very first mailing!',
            'reply_to' => 'alex.ankit@ithands.net',
            'attachments' => $attachments
        ],
        'substitution_data' => $substitutionData,
        'recipients' => $recipients,
        'cc' => [
            /*[
                'address' => [
                    'name' => 'Ankit1',
                    'email' => 'alex.ankit+11@ithands.net',
                ],
            ],*/
        ],
    ];

    $options=array_filter($options);

    $apiKey=getenv('SP_API_KEY');

    $SparkPost = new SPHelper($apiKey);
    $SparkPost->setOptions($options);
    print_r($SparkPost->options);
    $SparkPost->debugMode();
    $response = $SparkPost->sendEmail();
}

?>

<section class="row">
    <div class="col-md-6 col-sm-12 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Send Email Form</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Send Email</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="email" name="email" placeholder="Email">
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-4">
                            <input class="btn btn-default" type="submit" name="sendEmail" value="Send">
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</section>


<?php require_once('layouts/header.php'); ?>

