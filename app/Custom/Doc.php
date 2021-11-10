<?php
namespace App\Custom;

class Doc{
    function __construct($base_64file){
        $this->file = $base_64file;
        return $this;
    }

    public function save($filename){
        if(strlen($this->file) < 100){
            return json_encode(array(
                'status'=>false,
                'message'=>'Seems like the file is not a valid base64 file. Please try again.'
            ));
        }else{
            //check if file already exists
            if(file_exists(public_path($filename))){
                //delete this file
                @unlink(public_path($filename));
            }

            $fileExact = explode(',', $this->file)[1];
            $file = base64_decode($fileExact);
            $location = fopen(public_path($filename), "a");
            $success = fwrite($location, $file);
            fclose($location);

            return json_encode(array(
                'status'=>$success?'success': 'fail',
                'message'=>$success? 'Your file was successfully uploaded.': 'Unable to upload your file.Please try again.'
            ));
        }
    }
}
