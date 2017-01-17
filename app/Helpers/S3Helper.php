<?php

namespace App\Helpers;

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

class S3Helper{

    /**
     * AWS S3 class
     * @var S3Client
     */
    protected $s3;

    /**
     * Contains the bucket to be used
     * @var string
     */
    protected $bucket;

    /**
     * File path of the bucket
     * @var string
     */
    protected $file_path;

    /**
     * Setting the S3 Helper class with definition
     * @param string $file_path
     */
    public function __construct($file_path = 'temp')
    {
        $this->file_path = $file_path;

        // Create new instance of S3Client
        $this->s3 = new S3Client([
            'credentials' => CredentialProvider::env(),
            'region'    => 'us-west-2',
            'version'   => '2006-03-01',
            'signature_version' => 'v4'
        ]);

        //setting the bucket information according to enviorment
        $this->bucket = getenv('S3_BUCKET');

        $this->createDirectory($this->file_path);
        $this->bucket=$this->createBucket($this->bucket);
    }

    public function setFilePath ($file_path)
    {
        $this->file_path = $file_path;

        return $this;
    }

    /**
     * Function Saves the file in S3 bucket
     * @param  string $file_temp_name uploaded file
     * @param  string $file_name new file name
     * @return mixed            [false if using default else the s3 result]
     */

    public function saveFile($file_temp_name, $file_name)
    {
        //echo $this->file_path;
        //if environment is local then savin in local directory
        if(getenv('APP_ENV') == 'local')
        {
            return $this->saveFileDefault($file_temp_name, $file_name);
        }

        //using s3 to save the information
        return $this->s3->putObject([
            'Bucket'        => $this->bucket,
            'Key'           => $this->file_path . '/' . $file_name,
            'SourceFile'    => $file_temp_name,
            'ACL'           => 'public-read'
        ]);
    }

    /**
     * Function retrieves the file from s3
     * @param  string $file_name [file to be retrieved]
     * @return array            [result from s3]
     */
    public function getFile($file_name)
    {
        if(getenv('APP_ENV') == 'local')
        {
            return $this->getFileUrl($file_name);
        }
        $destination=$this->createDirectory('temp');
        return $this->s3->getObject([
            'Bucket' => $this->bucket,
            'Key' => $this->file_path . '/' . $file_name,
            'SaveAs' => $destination.$file_name
        ]);
    }

    /**
     * @param $file_name
     * @return string
     */
    public function getFileUrl($file_name)
    {
        if(getenv('APP_ENV') == 'local')
        {
            return $this->getFileUrlDefault($file_name,$this->file_path);
        }
        return $this->s3->getObjectUrl($this->bucket , $this->file_path . '/' .$file_name);
    }

    /**
     * @param $file_name
     * @param $directory
     * @return string
     */
    private function getFileUrlDefault($file_name,$directory)
    {
        $http=isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        return $http . $_SERVER['HTTP_HOST'] . '/uploads/' . $directory . '/' . $file_name;
    }

    /**
     * Function deletes the file form s3 according to env
     * @param  string $file_name [file name to be deleted]
     * @return mixed            [false if default setting else s3 result]
     */
    public function deleteFile($file_name)
    {
        //if environment is local then savin in local directory
        if(getenv('APP_ENV') == 'local'){
            $this->deleteFileDefault($file_name);

            return false;
        }

        return $this->s3->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $this->file_path . '/' . $file_name
        ]);
    }

    public function deleteTempFile($file_name)
    {
        $this->setFilePath('temp')->deleteFileDefault($file_name);
    }

    /**
     * @param $file_name
     * @return string
     */
    public function getFileContent($file_name)
    {
        if($this->doesFileExist($file_name))
        {
            $file_url = $this->getFileUrl($file_name);
            return base64_encode((file_get_contents($file_url)));
        }

        return false;
    }

    /**
     * Function is fallback to default saving system
     * @param  string $file_temp_name      [uploaded file information]
     * @param  string $file_name [new file name]
     * @return bool
     */
    private function saveFileDefault($file_temp_name, $file_name)
    {
        $destination= $this->createDirectory($this->file_path);

        if(!move_uploaded_file($file_temp_name,$destination.$file_name)){
            return true;
        }
        return true;
    }

    /**
     * Checks if a file exists in S3 Bucket
     * @param $file_name
     * @return mixed
     */
    public function doesFileExist($file_name)
    {
        if(getenv('APP_ENV') == 'local')
        {
            $destination= $this->createDirectory($this->file_path);
            return file_exists($destination.$file_name);
        }

        return $this->s3->doesObjectExist($this->bucket,$this->file_path.'/'.$file_name);
    }

    /**
     * Deletes the file using the default settings
     * @param  string $file_name [the file name]
     */
    private function deleteFileDefault($file_name)
    {
        $destination= $this->createDirectory($this->file_path);

        //if file exists then deleting it
        if (file_exists($destination.$file_name))
            unlink($destination.$file_name);
    }

    /**
     * Generate directory with read write permissions if not exists else return directory path.
     *
     * @param string $directory_name
     * @return string $directory_path
     */
    private function createDirectory($directory_name = null)
    {
        $directory_path = UPLOAD_DIR_PATH .'/'. $directory_name . "/";
        if (!is_dir($directory_path))
        {
            mkdir($directory_path, 0755, TRUE);
            return $directory_path;
        }
        else
        {
            return $directory_path;
        }
    }

    /**
     * Checks for existence and Create the bucket
     * @param $bucket
     * @return mixed
     */
    private function createBucket($bucket)
    {
        if(!$this->s3->doesBucketExist($bucket))
            $this->s3->createBucket(array(
                'Bucket'             => $bucket,
                'LocationConstraint' => 'us-west-2',
                'ACL' => 'public-read'
            ));

        return $bucket;
    }
}
