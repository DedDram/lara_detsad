@extends('layouts')
@section('styles')
    @parent
    <link href="{{ asset('/css/cattable.css') }}" rel="stylesheet">
@endsection
@section('content')
    @if(!empty($items))
        <table style="width:100%;border:0;text-align:center" class="luchie">
            <tbody>
            <tr>
                <td class="contentdescription" colspan="2"></td>
            </tr>
            <tr>
                <td>
                    <table class="display" id="cattable">
                        <thead>
                        <tr>
                            <th class="sectiontableheader" style="text-align:right;width:5%">N</th>
                            <th class="sectiontableheader" style="text-align:left;">Название урока</th>
                        </tr>
                        </thead>
                        @foreach ($items as $item)
                            <tr class="sectiontableentry">
                                <td data-label="№">{{$loop->iteration}}</td>
                                <td><a href="/zanyatiya/{{$category_id}}-{{$category_alias}}/{{$item->id}}-{{$item->alias}}">{{$item->title}}</a></td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    @endif
@endsection

