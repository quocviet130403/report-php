<?php
class Translation{
    private $lang_code;
    private $translation;

    function __construct($lang_code){
        $this->lang_code = $lang_code;

        if(file_exists(__DIR__.'/translation/'.$this->lang_code.'.json')){
            $trans_data = file_get_contents(__DIR__.'/translation/'.$this->lang_code.'.json');
            $this->translation = json_decode($trans_data, true);
        }
        else{
            $trans_data = file_get_contents(__DIR__.'/translation/en.json');
            $this->translation = json_decode($trans_data, true);
        }
    }

    function phrase($key){
        return $this->translation[$key];
    }
    function get_trans(){
        return $this->translation;
    }
}
?>