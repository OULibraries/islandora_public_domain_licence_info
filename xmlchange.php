<?php
// This script adds the license informaiton to the datastreams of the islandora objects belonging to an islandora collection:

if(empty($argv[1])){
        print("\n File path named by the collection PID is not specified!\n ");
        exit();
}

if(empty($argv[2])){
        print("\n Datastream type is not specified!\n");
        exit();
}

$ds_type = $argv[2];

$file_path = $argv[1];

if($handle = opendir($file_path)){
        while(false !== ($entry = readdir($handle))){
                $ob_file_path = $file_path . "/" . $entry;
                if(is_dir($ob_file_path) && false !== strpos($entry, "islandora") && $ob_handle = opendir($ob_file_path)){
                        //echo "\n object directory path = $ob_file_path\n";
                        while(false !== ($ob_entry = readdir($ob_handle))){
                                if(false !== strpos($ob_entry, $ds_type)){

                                    $ds_file_path = $ob_file_path . "/" . $ob_entry;
                                    $content = (file_get_contents($ds_file_path));
                                    $xml = new DOMDocument();
                                    $xml->load($ds_file_path);
                                    $xml_root = $xml->firstChild;
                                    if($ds_type == "MODS" && $xml_root->getElementsByTagName("accessCondition")->length == 0){
                                        if(!$xml_root->hasAttribute("xmlns:xlink")){
                                            $xml_root->setAttribute("xmlns:xlink","http://www.w3.org/1999/xlink");
                                        }
                                        $accessCondition = $xml->createElement("accessCondition");
                                        $accessConditionTextnode = $xml->createTextNode("To the extent possible under law, the person who associated CC0 with this work has waived all copyright and related or neighboring rights to this work. This work is published from: United States. (http://creativecommons.org/publicdomain/zero/1.0/)");
                                        $accessCondition->appendChild($accessConditionTextnode);
                                        $accessCondition->setAttribute("type","use and reproduction");
                                        $accessCondition->setAttribute("xlink:href", "http://creativecommons.org/publicdomain/zero/1.0/");
                                        $xml_root->appendChild($accessCondition);
                                        $xml = $xml->saveXML();
                                        file_put_contents($ds_file_path, $xml);
                                    }
                                    elseif($ds_type == "DC" && $xml_root->getElementsByTagName("rights")->length == 0){
                                        $rights = $xml->createElement("dc:rights");
                                        $rightsTextnode = $xml->createTextNode("public domain");
                                        $rights->appendChild($rightsTextnode);
                                        $rights = $xml_root->appendChild($rights);
                                        $result = "";
                                        foreach ($xml->childNodes as $node) {
                                            $result .= $xml->saveXML($node)."\n";
                                        }
                                        if(file_put_contents($ds_file_path, $result) === false){
                                            echo "ERROR : cannot save the DOMDocument into DC file!";
                                        }
                                    }
                                    
                                    //file_put_contents($ds_file_path, $newXml);
                                }
                        }
                }
        }
}