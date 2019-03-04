<?php
/*
 * PQL (Paperless Query Language) is a PHP Open Source project hosted on GitHub.
 * PQL is developed by Ghazi Khan (https://github.com/mgks) under MIT Open Source License.
 * This program is free to use for private and commercial purposes.
 * It will be generous to mention project source or developer in your Application's License(s) Wiki.
 * Giving right credit to developers encourages them to create better projects :)
*/

//error_reporting(-1);
//ini_set('display_errors', 'On');

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
        echo $v['req_t']."<br>";
        echo $v['data']['values']."<br>";
        echo $v['data']['table']."<br>";
        echo $v['data']['table_t']."<br>";
        echo $v['data']['cond']['col']."<br>";
        echo $v['data']['cond']['del']."<br>";
        echo $v['data']['cond']['val']."<br>";
        echo $v['data']['ord']."<br>";
        echo $v['data']['lim']."<br>";
        //print_r($v);
        /*        if($v['status']){
            return $this->sel($v['data']);
        }else{
            $this->throw_error();
        }*/
    }

    // create new table
    function cre($data){
        
    }

    // add to table
    function add($data){
        $table = $data['table'];
        $values = $data['values'];
        $temp_table = $data['temp_table'];

        $temp = fopen($temp_table, "w") or die($this->throw_error(6));
        if(fwrite($temp, $values)){
            $content = file_get_contents($temp_table);
            file_put_contents($this->database, $content, FILE_APPEND);
        }


        //$handle = fopen($this->database, 'w') or die('cannot open file: '.$this->database);
        //$data = $query;
        if(fwrite($handle, $data)){
            return true;
        }else{
            return false;
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
        $ar = array();
        $ar['status'] = true;
        if(file_exists($this->database)){
            preg_match("/([a-z]+)\s+([a-z0-9,_:\s]+)\s(in|from)\s([a-z_]+)(\s(where)\s([a-z_]+)\s?([=><])\s?([0-9]+))?(\s(ord)\s(asc|desc))?(\s(lim)\s([0-9]))?/i", $query, $chop);

            if($chop[0]!=$query){
                $te = $this->throw_error(1, $chop[0]);
                $ar['status'] = false;
                $ar['error'] = $te['error'];
            }else{
                $ar['req_t']            = $chop[1];
                $ar['data']['values']   = $chop[2];
                $ar['data']['table']    = $chop[4];
                $ar['data']['table_t']  = "temp_".$chop[4];

                $ar['data']['cond']['col'] = empty($chop[7])?null:$chop[7];
                $ar['data']['cond']['del'] = empty($chop[8])?null:$chop[8];
                $ar['data']['cond']['val'] = empty($chop[9])?null:$chop[9];

                $ar['data']['order']    = empty($chop[12])?null:$chop[12];
                $ar['data']['limit']    = empty($chop[15])?null:$chop[15];
            }
        }else{
            $ar['status'] = false;
            if($this->automate){
                $this->new_pql();
            }else{
                $te = $this->throw_error(2);
                $ar['error'] = $te['error'];
            }
        }
        return $ar;
    }

    // data output
    private function output($output){
        return $output;
    }

    // throwing errors
    private function throw_error($err_code = 0, $message = ""){
        $ar = array();
        $error = "<code>";
        switch($err_code){
            case 1: //validate(); query error
            $error .= 'there\'s an error near <b style="text-decoration:underline;color:red">'.$message.'</b>, please fix this to execute the program correctly.';
            break;

            case 2: //validate(); database file existence
            $error .= "<b>db.pql</b> file does not exists, use <b style=\"color:green\">new_pql()</b> to (re)create database file.";
            break;

            case 3: //new_pql(); database non-forced creation fail
            $error .= "database already exists, try a different name or force database recreation with <b style=\"color:blue\">new_pql(<i>\"_db_name_\", true</i>)</b>.";
            break;

            case 4: //new_pql(); database creation success
            $error .= "database creation successful <b>".$message."</b>.";
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
        $ar['code'] = $err_code;
        $ar['error'] = $error;
        return $ar;
    }
}

$pql = new PQL();
//$pql->new_pql();
//$pql->database = "db.pql";
//$pql->throw_error = false;
//echo $pql->cre("batman, superman, arithematica gotham");
echo "<br>CREATE:<br>";
echo $pql->que("CRE id, facebook, time IN bat_gadgets");
echo "<br>ADD:<br>";
echo $pql->que("ADD 1, batmobil, :time IN batman");
echo "<br>UPDATE:<br>";
echo $pql->que("UPD 1, batmobil, :time IN batman where id=4");
echo "<br>SELECT:<br>";
echo $pql->que("SEL 43 FROM batman where active=1 ord desc lim 5");
echo "<br>DELETE:<br>";
echo $pql->que("DEL 23 FROM batman");
//echo $pql->sel("id, name");
?>