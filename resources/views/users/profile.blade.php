@extends('layouts')
@section('robots')
    @parent
    <meta name="robots" content="noindex, nofollow" />
@endsection
@section('scripts')
    <!-- Подключение bootstrap.js -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <!-- Ваш собственный JavaScript файл -->
    <script src="{{ asset('js/channel.js') }}"></script>
@endsection
@section('content')
    <h1>Личный кабинет</h1>
    <form method="POST" action="/update-profile">
        @csrf
        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">Имя (видно в отзывах, можно изменить)</label>
            <div class="col-md-6">
                <input id="name" type="text" class="form-control " name="name" value="{{$user->name}}" autocomplete="name" autofocus="" style="width: 250px;">
            </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">Пароль (если оставите пустым, пароль останется прежним</label>
            <div class="col-md-6">
                <input id="password" style="width: 250px;" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    Обновить
                </button>
            </div>
        </div>
    </form>
    @if($agent)
    <div style="margin-top: 3%">
        <h3>Вы представитель садика</h3>
        <a href="{{$agent->url}}">{{$agent->name}}</a>
    </div>
    @endif
    <div style="margin-top: 3%">
        <h3>5 ваших последних отзывов</h3>
        @if($comments->isEmpty())
            Вы пока не написали ни одного отзыва...
        @else
            @foreach($comments as $comment)
               <div> <a href="{{(\App\Models\DetSad\Item::getUrlSadik($comment->object_id))->url}}">{{$comment->created}}</a>  - {{Str::limit(strip_tags($comment->description), 150).'...'}}</div>
            @endforeach
        @endif
    </div>
@endsection

