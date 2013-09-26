<?php
# script is using json format which is generated in that project: https://github.com/zejn/prometapi.git

# known public deploys:
#    http://prevoz.org/api/bicikelj/list/
#    http://opendata.si/promet/bicikelj/list/

# if you want to have your own server which will poll from original source, here are short instructions for ubuntu  
#    apt-get install python-django
#    apt-get install python-simplejson
#    apt-get install python-lxml
#    apt-get install libgeos-dev
#    apt-get install sqlite3 

#    git clone https://github.com/zejn/prometapi.git
#    python manage.py syncdb
#    python manage.py runserver 0.0.0.0:8000

# add following command on your crontab 
#    python prometapi/manage.py bicikelj_fetch

# you can reach required json here
#    http://<your ip>:8000/promet/bicikelj/list/



# script for cacti scriptserver

/* do NOT run this script through a web browser */
if (!isset($_SERVER["argv"][0]) || isset($_SERVER['REQUEST_METHOD'])  || isset($_SERVER['REMOTE_ADDR'])) {
	die("<br><strong>This script is only meant to run at the command line.</strong>");
}

$no_http_headers = true;

/* display errors */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

if (isset($config)) {
	include_once(dirname(__FILE__) . "/../lib/snmp.php");
}

if (!isset($called_by_script_server)) {
	include_once(dirname(__FILE__) . "/../include/global.php");
	include_once(dirname(__FILE__) . "/../lib/snmp.php");

	array_shift($_SERVER["argv"]);

	print call_user_func_array("ss_bicikelj", $_SERVER["argv"]);
}

function ss_bicikelj($cmd, $arg1 = "", $arg2 = "") {
        global $jsonData;
        # change it to your server if you want to use your server for polling from original source
        $url = "http://opendata.si/promet/bicikelj/list/";
        if (!isset($jsonData)) {
             $jsonData = json_decode(file_get_contents($url));
        }

        $props = get_object_vars($jsonData->markers);

	if ($cmd == "index") {
		foreach ($props as $prop) {
                    print str_replace(" ", "_", $prop->name)."\n";
		}
	}elseif ($cmd == "query") {
                $end_str = "";
		foreach ($props as $prop) {
                    if ($arg1 == "index") {
                        $end_str .= str_replace(" ", "_", $prop->name)."!".str_replace(" ", "_", $prop->name)."\n";
                    } else if ($arg1 == "fullAddr") {
                        $end_str .= str_replace(" ", "_", $prop->name)."!".$prop->fullAddress."\n";
		    }
		}
                return $end_str; 
	}elseif ($cmd == "get") {
		$index = $arg2;
 	        foreach ($props as $prop) {
	            if (str_replace(" ", "_", $prop->name) == $index) {
                        if ($arg1 == "total") {
                            return $prop->station->total;
                        } else if ($arg1 == "free") {
                            return $prop->station->free;
                        } else if ($arg1 == "available") {
                            return $prop->station->available;
                        }
                    }
                }
	}
}

?>
