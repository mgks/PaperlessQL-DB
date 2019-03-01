<?php
/*
 * PQL (Paperless Query Language) is a PHP Open Source project hosted on GitHub.
 * PQL is developed by Ghazi Khan (https://github.com/mgks) under MIT Open Source License.
 * This program is free to use for private and commercial purposes.
 * It will be generous to mention project source or developer in your Application's License(s) Wiki.
 * Giving right credit to developers encourages them to create better projects :)
*/

error_reporting(-1);
ini_set('display_errors', 'On');

class PQL{
    
    private $output = NULL;         //output variable
    private $pql_dir = "pql_dir";   //PQL's working directory to save databases and config files
    private $db_name = "db";        //default database name
    private $db_ext = ".pql";       //PQL default file type
    private $automate = false;      //automate PQL process
    private $throw_error = true;    //throw tailored errors on screen

    private $database = null;

    function __construct($db = null){
        $this->db_name = $db ? $db : $this->db_name;

        //active database location; we'll be calling this for further requests
        $this->database = $this->pql_dir."/".$this->db_name.$this->db_ext;
    }

    // (re)create database file
    function new_pql($db = null, $force = false){
        $db = $this->pql_dir."/".($db ? $db : $this->db_name).$this->db_ext;
        if(file_exists($db) && !$force){
            $this->throw_error(3);
        }else{
            if(!file_exists($this->pql_dir)){
                mkdir("".$this->pql_dir."", 0777, true);
            }
            fopen($db, "w") or die($this->throw_error(5));
            if(file_exists($db)){
                $this->throw_error(4, $db);
            }else{
                $this->throw_error(5);
            }
        }
    }

    // query function
    function que($query){
        $v = $this->validate($query);
        if($v['status']){
            switch($v['req_t']){
                case 1:
                return $this->sel($v['']);
            }
            if($v['req_t'] == 1){
                return $this->sel();
            }else if($v['req_t'] == 2){

            }
        }else{
            $this->throw_error();
        }
    }

    // create new table
    function cre($query){
        if($this->validate($query)){
            $output = "";
            $this->output($output);
        }
    }

    // add to table
    function add($query){
        $this->validate($query);
        if($v['status']){
            $content = file_get_contents($this->database);
            file_put_contents($this->database, $content, FILE_APPEND);
            //$handle = fopen($this->database, 'w') or die('cannot open file: '.$this->database);
            //$data = $query;
            if(fwrite($handle, $data)){
                return true;
            }else{
                return false;
            }
        }
    }

    // alter table data
    function alt($query){
    }

    // select table data
    function sel($query){
        if($this->validate($query)){
            $db = fopen($this->database, 'r');
            $output = fread($db, filesize($this->database));
            return $this->output($output);
        }else{
            $this->throw_error();
        }
    }

    // delete table row
    function del($query){
    }

    // validating requested query
    private function validate($query){
        if(file_exists($this->database)){
            if(preg_match("/([0-9a-zA-Z])\s+([0-9a-zA-Z])/", $query, $matches)){
                $this->throw_error(1, $matches[0]);
                return false;
            }else{
                return true;
            }       
        }else{
            if($this->automate){
                $this->new_pql();
            }else{
                $this->throw_error(2);
            }
            return false;
        }
    }

    // data output
    private function output($output){
        return $output;
    }

    // throwing errors
    private function throw_error($err_code = 0, $error = ""){
        $error .= "<code>";
        switch($err_code){
            case 1: //validate(); query error
            $error .= "there's an error near <b style=\"text-decoration:underline;color:red\">".$error."</b>, please fix this to execute the program correctly.";
            break;

            case 2: //validate(); database file existence
            $error .= "<b>db.pql</b> file does not exists, use <b style=\"color:green\">new_pql()</b> to (re)create database file.";
            break;

            case 3: //new_pql(); database non-forced creation fail
            $error .= "database already exists, try a different name or force database recreation with <b style=\"color:blue\">new_pql(<i>\"_db_name_\", true</i>)</b>.";
            break;

            case 4: //new_pql(); database creation success
            $error .= "database creation successful <b>".$error."</b>.";
            break;

            case 5: //new_pql(); database creation failure
            $error .= "database creation failed.";
            break;

            default: //default
            $error .= "invalid syntax!";
            break;
        }
        $error .= "</code>";
        if($this->throw_error){
            echo $error;
        }
    }
}

$pql = new PQL();
//$pql->new_pql();
//$pql->database = "db.pql";
//$pql->throw_error = false;
//echo $pql->cre("batman, superman, arithematica gotham");
echo $pql->add("15, ffg");
echo $pql->sel("id, name");
?>