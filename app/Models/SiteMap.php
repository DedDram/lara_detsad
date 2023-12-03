<?php

namespace App\Models;

use DOMDocument;
use Illuminate\Support\Facades\DB;

class SiteMap
{
    public array $files = [];

    /**
     * @throws \DOMException
     */
    public function __construct()
    {
        $this->getDetsad();
        $this->getDistrictsMetro();
        $this->getStreets();

        $this->save();
    }


    /**
     * @throws \DOMException
     */
    private function getDetsad(): void
    {
        $items = DB::table('i1il4_detsad_items AS t1')
            ->select('t1.modified',
                DB::raw('CONCAT_WS('-', t1.id, t1.alias) AS item_alias'),
                DB::raw('CONCAT_WS('-', t2.id, t2.alias) AS category_alias'),
                DB::raw('CONCAT_WS('-', t3.id, t3.alias) AS section_alias'),
            )
            ->join('i1il4_detsad_categories AS t2', 't1.category_id', 't2.id')
            ->join('i1il4_detsad_sections AS t3', 't1.section_id', 't3.id')
            ->orderBy('t1.id', 'ASC')
            ->get()
            ->toArray();

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

                $result[] = (object) array(
                    'loc' => 'https://detskysad.com/'.$item->section_alias.'/'.$item->category_alias.'/'.$item->item_alias,
                    'lastmod' => $lastmod
                );
            }
            $this->setXml($result, 'sitemap_detsad_'.$n.'.xml', $max);
        }
    }


    /**
     * @throws \DOMException
     */
    private function getDistrictsMetro(): void
    {
        $d_items = array();

        $results = DB::table('i1il4_detsad_districts AS t1')
            ->select('t1.parent', 't1.alias',
            DB::raw("CONCAT(t2.id, '-', t2.alias) as city")
            )
            ->leftJoin('i1il4_detsad_categories AS t2', 't1.parent', 't2.name')
            ->get();

        $metros = DB::table('i1il4_detsad_metro')
            ->select(DB::raw("CONCAT_WS('-', id, alias)"))
            ->get()
            ->pluck('metros_alias');

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


    /**
     * @throws \DOMException
     */
    private function getStreets(): void
    {
        $result = array();
        $a_streets = array();
        $streets = DB::table('i1il4_detsad_address AS t1')
            ->select('t1.street_alias AS alias', 't1.locality', 't3.id AS city_id', 't3.alias AS city')
            ->leftJoin('i1il4_detsad_items AS t2', 't1.item_id', 't2.id')
            ->leftJoin('i1il4_detsad_categories AS t3', function ($join) {
                $join->on('t1.locality', '=', 't3.name')
                    ->on('t2.category_id', '=', 't3.id');
            })
            ->where('t1.street_alias', '!=', '')
            ->get();

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


    /**
     * @throws \DOMException
     */
    private function setXml($items, $filename, $lastmod): void
    {
        $this->files[] = (object) array('name' => $filename, 'lastmod' => $lastmod);

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
            $row->appendChild($aElement);
            if(!empty($item->lastmod)){
                $bElement = $xml->createElement('lastmod', $item->lastmod);
                $row->appendChild($bElement);
            }
        }
        $xml->formatOutput = true;
        $xml->save(public_path('/xml/'.$filename));
    }

    /**
     * @throws \DOMException
     */
    private function save(): void
    {
        $xml = new DomDocument('1.0','utf-8');

        $rssElement = $xml->createElement('sitemapindex');
        $rssAttribute = $xml->createAttribute('xmlns');
        $rssAttribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $rssElement->appendChild($rssAttribute);
        $rss = $xml->appendChild($rssElement);

        foreach($this->files as $file)
        {
            $rowsElement = $xml->createElement('sitemap');
            $row = $rss->appendChild($rowsElement);

            $aElement = $xml->createElement('loc', 'https://detskysad.com/xml/'.$file->name);
            $row->appendChild($aElement);

            $bElement = $xml->createElement('lastmod', $file->lastmod);
            $row->appendChild($bElement);
        }
        $xml->formatOutput = true;
        $xml->save(public_path('/xml/sitemap.xml'));
    }
}
