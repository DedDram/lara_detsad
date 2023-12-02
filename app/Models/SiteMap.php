<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class SiteMap
{
    public array $files = [];

    public function __construct()
    {
        $this->getDetsad();
        $this->getContent();
        $this->getDistrictsMetro();
        $this->getStreets();

        $this->save();
    }



    public function getContent(): void
    {
        $items = DB::table('i1il4_content as t1')
            ->select(
                't1.*',
                DB::raw("CONCAT('/', t2.alias, '/', t1.id, '-', t1.alias) as url")
            )
            ->join('i1il4_categories as t2', 't2.id', '=', 't1.catid')
            ->get()
            ->toArray();

        $data = array_chunk($items, 42000);

        foreach($data as $n => $rows)
        {
            $max = 0;
            $result = array();
            foreach($rows as $item)
            {

                $url = $item->url;
                $timestamp = strtotime($item->modified);
                if($timestamp > 0)
                {
                    $lastmod = date('c', $timestamp);
                }else{
                    $lastmod = date('c', 1377029410);
                }
                if($lastmod > $max)
                {
                    $max = $lastmod;
                }

                $result[] = (object) array(
                    'loc' => 'https://detskysad.com'.$url,
                    'lastmod' => $lastmod
                );
            }
            $this->setXml($result, 'sitemap_content_'.$n.'.xml', $max);
        }
    }

    /*
     *
     */

    function getDetsad()
    {
        $this->_db->setQuery("SELECT t1.modified, CONCAT_WS('-', t1.id, t1.alias) AS item_alias, CONCAT_WS('-', t2.id, t2.alias) AS category_alias, CONCAT_WS('-', t3.id, t3.alias) AS section_alias FROM #__detsad_items AS t1 INNER JOIN #__detsad_categories AS t2 ON t1.category_id = t2.id INNER JOIN #__detsad_sections AS t3 ON t1.section_id = t3.id ORDER BY t1.id ASC");
        $items = $this->_db->loadObjectList();

        $data = array_chunk($items, 42000);

        foreach($data as $n => $rows)
        {
            $max = 0;
            $result = array();
            foreach($rows as $item)
            {
                //
                $timestamp = strtotime($item->modified);
                if($timestamp > 0)
                {
                    $lastmod = date('c', $timestamp);
                }else{
                    $lastmod = date('c', 1377029410);
                }
                if($lastmod > $max)
                {
                    $max = $lastmod;
                }
                //
                $result[] = (object) array(
                    'loc' => 'https://detskysad.com/'.$item->section_alias.'/'.$item->category_alias.'/'.$item->item_alias,
                    'lastmod' => $lastmod
                );
            }
            $this->setXml($result, 'sitemap_detsad_'.$n.'.xml', $max);
        }
    }

    /*
     *
     */

    function getDistrictsMetro()
    {
        $this->_db->setQuery("SELECT t1.parent, t1.alias, CONCAT(t2.id, '-', t2.alias) as city FROM #__detsad_districts as t1 LEFT JOIN #__detsad_categories as t2 ON t1.parent =  t2.name");
        $results = $this->_db->loadObjectList();
        $this->_db->setQuery("SELECT CONCAT_WS('-', id, alias) FROM #__detsad_metro");
        $metros = $this->_db->loadColumn();

        foreach($results as $result){
            $d_items[$result->parent][] = $result->city.'/'.$result->alias;
        }

        $result = array();
        foreach($d_items as $key=>$d_item){
            if(count($d_item)>1){
                foreach($d_item as $item){
                    $result[] = (object) array(
                        'loc' => 'https://detskysad.com/1-russia/'.$item
                    );
                }
            }
        }
        foreach($metros as $metro){
            $result[] = (object) array(
                'loc' => 'https://detskysad.com/metro/'.$metro
            );
        }

        $this->setXml($result, 'sitemap_detsad_dm.xml', date('c'));
    }

    /*
     *
     */

    function getStreets()
    {
        $streets = array();
        $a_streets = array();
        $this->_db->setQuery("SELECT t1.street_alias AS alias, t1.locality, t3.id AS city_id, t3.alias AS city FROM #__detsad_address AS t1 LEFT JOIN #__detsad_items AS t2 ON t1.item_id = t2.id LEFT JOIN #__detsad_categories AS t3 ON t2.category_id = t3.id AND t1.locality=t3.name WHERE t1.street_alias<>'' ORDER BY t1.locality, alias");
        $streets = $this->_db->loadObjectList();
        foreach($streets as $street){
            if($street->locality == 'Москва'){
                $street->city_id = 1;
                $street->city = 'moskva';
            }
            $a_streets[$street->city_id.':'.$street->alias][] = $street;
        }
        foreach($a_streets as $a_street){
            if(count($a_street)>1){
                $result[] = (object) array(
                    'loc' => 'https://detskysad.com/street/'.$a_street[0]->city_id.'-'.$a_street[0]->city.'/'.$a_street[0]->alias
                );
            }
        }

        $this->setXml($result, 'sitemap_detsad_str.xml', date('c'));
    }

    /*
     *
     *
     *
     *
     */

    function setXml($items, $filename, $lastmod)
    {
        $this->files[] = (object) array('name' => $filename, 'lastmod' => $lastmod);
        //
        $xml = new DomDocument('1.0','utf-8');
        $rssElement = $xml->createElement('urlset');
        $rssAttribute = $xml->createAttribute('xmlns');
        $rssAttribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $rssElement->appendChild($rssAttribute);
        $rss = $xml->appendChild($rssElement);

        foreach($items as $item)
        {
            $rowsElement = $xml->createElement('url');
            $row = $rss->appendChild($rowsElement);

            $aElement = $xml->createElement('loc', $item->loc);
            $a = $row->appendChild($aElement);
            if(!empty($item->lastmod)){
                $bElement = $xml->createElement('lastmod', $item->lastmod);
                $b = $row->appendChild($bElement);
            }
        }
        $xml->formatOutput = true;
        $xml->save(JPATH_ROOT.'/xml/'.$filename);
    }

    function save()
    {
        $xml = new DomDocument('1.0','utf-8');
        //
        $rssElement = $xml->createElement('sitemapindex');
        $rssAttribute = $xml->createAttribute('xmlns');
        $rssAttribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $rssElement->appendChild($rssAttribute);
        $rss = $xml->appendChild($rssElement);
        //
        foreach($this->files as $file)
        {
            $rowsElement = $xml->createElement('sitemap');
            $row = $rss->appendChild($rowsElement);
            //
            $aElement = $xml->createElement('loc', 'https://detskysad.com/xml/'.$file->name);
            $a = $row->appendChild($aElement);
            //
            $bElement = $xml->createElement('lastmod', $file->lastmod);
            $b = $row->appendChild($bElement);
        }
        $xml->formatOutput = true;
        $xml->save(JPATH_ROOT.'/xml/sitemap.xml');
    }
}
