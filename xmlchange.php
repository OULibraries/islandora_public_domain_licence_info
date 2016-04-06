<?php
// This script adds the license informaiton to the datastreams of the islandora objects belonging to an islandora collection:

if(empty($argv[1])){
        print("\n Collection PID is not specified!\n ");
        exit();
}

if(empty($argv[2])){
        print("\n Datastream type is not specified!\n");
        exit();
}

$ds_type = $argv[2];

$file_path = "/tmp/" . $argv[1];

if($handle = opendir($file_path)){
        while(false !== ($entry = readdir($handle))){
                $ob_file_path = $file_path . "/" . $entry;
                if(is_dir($ob_file_path) && false !== strpos($entry, "islandora") && $ob_handle = opendir($ob_file_path)){
                        //echo "\n object directory path = $ob_file_path\n";
                        while(false !== ($ob_entry = readdir($ob_handle))){
                                if(false !== strpos($ob_entry, $ds_type)){
                            		$ds_file_path = $ob_file_path . "/" . $ob_entry;
                                    $xml=simplexml_load_file($ds_file_path) or die("Error: Cannot create object");
                                    $accessCondition = $xml->addChild("accessCondition","To the extent possible under law, the person who associated CC0 with this work has waived all copyright and related or neighboring rights to this work. This work is published from: United States. (http://creativecommons.org/publicdomain/zero/1.0/)");
                                    $accessCondition->addAttribute("type","use and reproduction");
                                    $accessCondition->addAttribute("xlink:href","http://creativecommons.org/publicdomain/zero/1.0/");
                                    $newXml = $xml->asXML();
                                    file_put_contents($ds_file_path, $newXml);
                                }
                        }
                }
        }
}
