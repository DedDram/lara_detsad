<?php

namespace App\Http\Controllers;

use App\Models\DetSadSections;

class DetSadController
{
    public function section($sectionId, $sectionAlias)
    {
        $sections = new DetSadSections();
        $section = $sections->getSection($sectionId);
        if (!empty($section)) {
            if ($sectionAlias != $section->alias) {
                return redirect()->to('/' . $sectionId . '-' . $section->alias);
            }
        } else {
            abort('404');
        }
        $categories = $sections->getCategories($sectionId);
        $address = $sections->getAddress($sectionId, $sectionAlias);
        return view('detsad.section',
            ['section' => $section,
                'categories' => $categories,
                'address' => $address,
                'title' => $section->title,
                'metaDesc' => $section->title . ' ❤️ отзывы о детских садах 😊 адреса на карте 🌎',
                'metaKey' => $section->title . ' отзывы, детские, сады',
            ]);
    }
}
