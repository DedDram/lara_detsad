@if($countComments > 3)
    <a href="#ADD" class="scomments-form-toogle scomments-add">✎... Добавить отзыв</a>
@endif
@if(!empty($items))
    <div class="scomments">
        {{ $items->links('vendor.pagination.custom-pagination') }}


        <div class="scomments-items">
            @if(!empty($good) || !empty($neutrally)|| !empty($bad))
                <div class="checked_comm_div" id="type_comments">
                    <label class="checked_comm">
                        <input type="radio" value="all" id="type_all" autocomplete="off" name="radio" checked="checked">
                        <span class="span_all">Все отзывы</span>
                        <span id="count_all">
                    {{$good+$neutrally+$bad}}
                </span>
                    </label>
                    <label class="checked_comm">
                        <input type="radio" value="good" id="type_good" name="radio" autocomplete="off">
                        <span class="good_all">Положительные</span>
                        <span id="count_good">{{$good}} ({{ round($procentGood).'%'}})</span>
                    </label>
                    <label class="checked_comm">
                        <input type="radio" value="neutrally" id="type_neutrally" name="radio" autocomplete="off">
                        <span class="neutrally_all">Нейтральные</span>
                        <span id="count_neutrally">{{$neutrally}} ({{round($procentNeutrally).'%'}})</span>
                    </label>
                    <label class="checked_comm">
                        <input type="radio" value="bad" id="type_bad" name="radio" autocomplete="off">
                        <span class="bad_all">Отрицательные</span>
                        <span id="count_bad">{{$bad}} ({{round($procentBad).'%'}})</span>
                    </label>
                </div>
            @endif
            <div class="scomments-all">
                @foreach($items as $item)
                    @if($countComments > 10 && $modulePosition == $loop->index)
                        <div class="scomments-item">
                            <div class="comments-content">
                                <div class="scomments-title">
					<span class="scomments-vote">
					</span>
                                    <div>
                                    </div>
                                </div>
                                <div>
                                </div>
                                <div class="scomments-text">
                                    <!-- module-comments -->
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($item->rate >= 4)
                        @php
                            $styleComments = 'good_comm';
                            $text_title = 'Хороший отзыв';
                            $smile = '😀';
                        @endphp
                    @elseif ($item->rate == 3 || $item->rate == 0)
                        @php
                            $styleComments = 'neutrally_comm';
                            $text_title = 'Нейтральный отзыв';
                            $smile = '😐';
                        @endphp
                    @else
                        @php
                            $styleComments = 'bad_comm';
                            $text_title = 'Плохой отзыв';
                            $smile = '😡';
                        @endphp
                    @endif
                        <div class="scomments-item {{$styleComments}}"{!! !empty($item->status) ? '' : ' style="background-color: #ffebeb;"' !!}>
                        @if(!empty($item->registered))
                            <div class="comments-avatar-registered"
                                 title="{{$text_title}} зарегистрированного пользователя"></div>
                        @else
                            <div class="comments-avatar-guest" title="{{$text_title}}"></div>
                        @endif
                        <div class="comments-content">
                            <div class="scomments-title">
                        <span class="scomments-vote">
						<a rel="nofollow" href="#" title="Согласен!" class="scomments-vote-good" data-id="{{$item->id}}"
                           data-value="up">Это правда{!! (!empty($item->isgood)) ? '<span>'.$item->isgood.'</span>': '' !!}</a>
						<a rel="nofollow" href="#" title="Не согласен!" class="scomments-vote-poor"
                           data-id="{{$item->id}}"
                           data-value="down">Это ложь{!! (!empty($item->ispoor)) ? '<span>'.$item->ispoor.'</span>': '' !!}</a>
					</span>
                                <div>
                                    <a href="#scomment-{{$item->id}}" name="scomment-{{$item->id}}"
                                       id="scomment-{{$item->id}}">#{{$item->n}}<span
                                            class="smile">{{$smile}}</span></a>
                                    @if(!empty($item->user_name))
                                        <span class="scomments-user-name" itemprop="author">{{$item->user_name}}</span>
                                    @else
                                        <span class="scomments-guest-name"
                                              itemprop="author">{{$item->guest_name}}</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <span class="scomments-date" itemprop="datePublished"
                                      content="{{$item->created}}">{{$item->created}}</span>
                                @if(!empty($item->country) && $item->country != 'unknown')
                                    <span class="scomments-marker"></span><span
                                        class="scomments-country">{{$item->country}}</span>
                                @endif
                            </div>
                            <div class="scomments-text"
                                 itemprop="reviewBody"> {!! \App\Helpers\CommentHelper::filterBadWords($item->description) !!}</div>

                            @if(!empty($item->images))
                                <a href="#" data-id="{{$item->id}}" class="scomments-item-images-toogle">Показать
                                    прикрепленное фото</a>
                                <div class="scomments-item-images"></div>
                            @endif

                            @if (Auth::check() && !empty($item->edit) && auth()->id() == $item->user_id)
                                <div class="scomments-button-edit"><a class="scomments-control-edit" data-task="edit"
                                                                      data-object-group="{{$item->object_group}}"
                                                                      data-object-id="{{$item->object_id}}"
                                                                      data-item-id="{{$item->id}}" href="#">/
                                        Редактировать отзыв</a>
                                </div>
                            @endif
                            <div class="scomments-button-quote"><a
                                    href="?num={{$item->n}}"
                                    class="scomments-form-toogle scomments-reply">Ответить</a></div>
                            @if (Auth::check() && Auth::user()->isAdmin())
                                <div class="scomments-control">
                                    <div class="scomments-control-msg"></div>
                                    <a class="scomments-control-edit" data-task="edit"
                                       data-object-group="{{$item->object_group}}"
                                       data-object-id="{{$item->object_id}}"
                                       data-item-id="{{$item->id}}" href="#"></a>
                                    <a class="scomments-control-delete" data-task="remove"
                                       data-object-group="{{$item->object_group}}"
                                       data-object-id="{{$item->object_id}}"
                                       data-item-id="{{$item->id}}" href="#"></a>
                                    <a class="scomments-control-unpublish" data-task="unpublish"
                                       data-object-group="{{$item->object_group}}"
                                       data-object-id="{{$item->object_id}}"
                                       data-item-id="{{$item->id}}" href="#"></a>
                                    <a class="scomments-control-publish" data-task="publish"
                                       data-object-group="{{$item->object_group}}"
                                       data-object-id="{{$item->object_id}}"
                                       data-item-id="{{$item->id}}" href="#"></a>
                                    <a class="scomments-control-blacklist" data-task="blacklist"
                                       data-object-group="{{$item->object_group}}"
                                       data-object-id="{{$item->object_id}}"
                                       data-item-id="{{$item->id}}" href="#"></a>
                                    <span class="scomments-control-ip">{{$item->ip}}</span>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {{ $items->links('vendor.pagination.custom-pagination') }}
    </div>
    <div style="margin-top: 5px;"></div>
    @if($countComments >= 5)
        <!-- reklama-over-10 -->
    @endif
@endif
<div class="scomments-anchor"></div>
<div class="scomments-form" id="ADD">
    @if (!empty($items[0]->blacklist))
        <h3>Администратор заблокировал возможность написания отзывов с этого IP - {{request()->ip()}}</h3>
        <p>Если вы считаете, что это произошло по ошибке - напишите на info@detskysad.com и укажите свой ip</p>
    @else
        @if (!empty($comments->reviews))
            <h3>Отзывы анонимных пользователей отключены</h3>
            <p>{{$comments->reviews}}</p>
        @else
            <header>Добавить отзыв</header>
            <div id="msg"></div>
            <div id="wrapper">
                <form id="myform" method="post" action="/post/comment">
                    @if (empty($comments->rate))
                        <div class="colLeft mob-spike">
                            <label>Проголосуйте</label>
                            <p>Вы еще не голосовали</p>
                        </div>
                        <div class="colRight">
                            <select name="star" class="starSelect">
                                <option value="0">выберите оценку ▼</option>
                                <option value="1">Ужасно</option>
                                <option value="2">Плохо</option>
                                <option value="3">Удовлетворительно</option>
                                <option value="4">Хорошо</option>
                                <option value="5">Отлично</option>
                            </select>
                        </div>
                        <div class="colClear"></div>
                    @endif

                    @if(!Auth::check())
                        <div class="colLeft">
                            <input type="text" name="username" id="username" placeholder="Ваше имя" value=""
                                   class="field">
                            <input type="text" name="email" id="email" placeholder="Ваш E-mail" value="" class="field">
                        </div>
                        <div class="colRight mob-spike">
                            <ul>
                                <li>
                                    <h4>Вы не авторизованы</h4>
                                    Введите ваши данные для обратной связи
                                </li>
                            </ul>
                        </div>
                        <div class="colClear"></div>
                    @endif
                    <ul class="mob-spike">
                        <li>Пишите развернутые отзывы, максимально описывая Вашу ситуацию.</li>
                        <li>Отзывы с оскорблениями будут удалены!</li>
                        <li>Запрещается копировать отзывы и стихи с других сайтов - они будут удалены!</li>
                        @if(Auth::check())
                            <li>С момента написания отзыва, у вас будет 15 минут, в течение которых вы сможете его
                                отредактировать.
                            </li>
                        @endif
                        @if(Auth::check() && Auth::user()->isAgent())
                            <li><b>Не нужно писать шаблонные отзывы</b>, например "Спасибо за ваш отзыв...", отвечайте
                                только когда это действительно необходимо.
                            </li>
                        @endif
                    </ul>
                    <textarea id="description" name="description" style="width: 99%; height: 150px;"></textarea>
                    @if(Auth::check())
                        <div style="margin: 10px 0;">
                            @if (!empty($comments->subscribe))
                                <input type="checkbox" name="subscribe" value="1"
                                       checked="checked"> Вы подписаны на уведомления о новых отзывах
                            @else
                                <input type="checkbox" name="subscribe"
                                       value="1"> Подписаться на уведомления о новых отзывах
                            @endif
                        </div>
                    @endif
                    @php
                        $attach = md5(uniqid('1'));
                    @endphp
                    <input type="hidden" id="task1" name="task" value="create">
                    <input type="hidden" name="item_id" value="">
                    <input type="hidden" name="object_group" value="{{$object_group}}">
                    <input type="hidden" name="object_id" value="{{$object_id}}">
                    <input type="hidden" name="attach" value="{{$attach}}">
                </form>
                <div class="colClear"></div>
                <div id="slider">
                    @if (!empty($comments->images))
                        @foreach ($comments->images as $image)
                            <div class="row-slide">
                                <a href="#" data-id="{{$image->id}}" data-attach="{{$attach}}"
                                   class="remove-slide"></a>
                                <img src="/images/comments/{{$image->thumb}}">
                            </div>
                        @endforeach
                    @endif
                </div>
                <label>Если хотите, можете добавить к своему отзыву фото</label>
                <form id="upload" action="/post/comment" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" id="file"> <span id="percent">0%</span>
                    <input type="hidden" name="option" value="com_comments">
                    <input type="hidden" name="view" value="images">
                    <input type="hidden" name="format" value="json">
                    <input type="hidden" id="task2" name="task" value="add">
                    <input type="hidden" name="attach" value="{{$attach}}>">
                    <input type="hidden" name="item_id" value="">
                </form>
                <div id="loader" style="text-align: center"></div>
                <input type="submit" name="submit" id="submit" value="Опубликовать отзыв">
            </div>
        @endif
    @endif


</div>
@if($countComments > 0)
    <!-- newsmobi -->
@endif
