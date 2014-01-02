<?php

include(__DIR__ . '/../init.inc.php');

class Updater
{
    public function main()
    {
        $map = $this->getIdMap();
        $data_list = $this->getDataList();
        $xml_list = $this->getDataXML();

        $type_list = explode(',', 'csv,txt,xml,word,excel,json,ftp,demdsm,kml,kmz,none,olap,pdf,png,rar,rss,shp,webquery,webservices,wmszip,other');

        $count = array();
        $count['kindName'] = array();
        $count['cateCodeName'] = array();
        $count['orgFullname'] = array();
        $count['dataTypeName'] = array();

        foreach ($map as $id_name) {
            list($id, $name) = $id_name;
            if (!array_key_exists($name, $xml_list)) {
                error_log("Not found: $name");
            }

            $types = array();
            foreach ($type_list as $type) {
                if ($xml_list[$name]->{$type}) {
                    $types[] = $type;
                }
            }
            $xml_list[$name]->{'types'} = $types;
            if ($xml_list[$name]->kindName) {
                $count['kindName'][$xml_list[$name]->kindName] ++;
            }
            if ($xml_list[$name]->cateCodeName) {
                $count['cateCodeName'][$xml_list[$name]->cateCodeName] ++;
            }
            if ($xml_list[$name]->orgFullname) {
                $count['orgFullname'][$xml_list[$name]->orgFullname] ++;
            }
            if ($xml_list[$name]->dataTypeName) {
                $count['dataTypeName'][$xml_list[$name]->dataTypeName] ++;
            }

            try {
                DataSet::insert(array(
                    'id' => $id,
                    'name' => $name,
                    'data' => json_encode($xml_list[$name]),
                ));
            } catch (Pix_Table_DuplicateException $e) {
                DataSet::find($id)->update(array(
                    'name' => $name,
                    'data' => json_encode($xml_list[$name]),
                ));
            }
        }
        KeyValue::set('count_group', json_encode($count));
    }

    public function getDataXML()
    {
        // http://data.gov.tw/sites/default/files/feeds/opendata0101.xml
        $doc = new DOMDocument;
        $doc->loadXML(file_get_contents('opendata0101.xml'));

        $ret = array();
        foreach ($doc->getElementsByTagName('RECORD') as $record_dom) {
            $obj = new StdClass;
            foreach ($record_dom->childNodes as $childNode) {
                if ('#text' == $childNode->nodeName) {
                    continue;
                }
                $obj->{$childNode->nodeName} = $childNode->nodeValue;
            }
            $ret[trim($obj->name)] = $obj;
        }
        return $ret;
    }

    public function getDataList()
    {
        // http://data.gov.tw/?q=node/6564
        $fp = fopen('datalist.csv', 'r');
        $columns = fgetcsv($fp);
        $ret = array();
        while ($row = fgetcsv($fp)) {
            $name = trim($row[0]);
            $type = trim($row[1]);
            if (!array_key_exists($name, $ret)) {
                $ret[$name] = array();
            }
            if (array_key_exists($type, $ret[$name])) {
                error_log('duplicate name: ' . $name);
            }
            $ret[$name][$type] = $row;
        }
        return $ret;
    }

    public function getIdMap()
    {
        // http://data.gov.tw/?q=data_usage/dataset/json
        $records = json_decode(file_get_contents('map.json'));
        //    {
        //       "資料集瀏覽人次" : "7",
        //       "資料集名稱" : "<a href=\"/?q=node/7665\">國際新聞讀報站-國際頭條新聞</a>"
        //    },
        $ret = array();
        foreach ($records as $record) {
            if (!preg_match('#^<a href="/\?q=node/(\d+)">(.*)</a>$#', $record->{'資料集名稱'}, $matches)) {
                error_log('Error: ' . $record->{'資料集名稱'});
                continue;
            }

            $ret[] = array(
                $matches[1],
                preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, trim($matches[2])) ,
            );
        }
        return $ret;
    }
}

$u = new Updater;
$u->main();
