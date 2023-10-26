var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const comments = (function () {
    const _private = {
        list: function () {
            /*Показ фото отзыва*/
            const imagesToogleElements = document.querySelectorAll(".scomments-item-images-toogle");
            imagesToogleElements.forEach(function (element) {
                element.addEventListener("click", function (e) {
                    e.preventDefault();
                    const id = this.getAttribute("data-id");
                    const el = this.parentNode.querySelector(".scomments-item-images");
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/post/comment', true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    const formData = new FormData();
                    formData.append('task', 'images');
                    formData.append('id', id);
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            el.innerHTML = '';
                            const rows = JSON.parse(xhr.responseText);
                            rows.forEach(function (row) {
                                const a = document.createElement('a');
                                a.href = '/storage/images/comments/' + row.original;
                                a.className = 'simplemodal';
                                a.setAttribute('data-width', '800');
                                a.setAttribute('data-height', '500');
                                a.innerHTML = '<img src="/storage/images/comments/' + row.thumb + '">';
                                el.appendChild(a);
                            });
                        }
                    };
                    xhr.send(formData);
                    el.style.display = el.style.display === "none" ? "block" : "none";
                });
            });

            /*голосование за отзыв*/
            const voteElements = document.querySelectorAll(".scomments-vote a");
            voteElements.forEach(function (element) {
                element.addEventListener("click", function (e) {
                    e.preventDefault();
                    const el = this.parentNode;
                    const id = this.getAttribute("data-id");
                    const value = this.getAttribute("data-value");
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/post/comment', true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    const formData = new FormData();
                    formData.append('task', 'vote');
                    formData.append('value', value);
                    formData.append('id', id);
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            const data = JSON.parse(xhr.responseText);
                            el.innerHTML = data.msg;
                        }
                    };
                    xhr.send(formData);
                });
            });

            /* Ответить на отзыв*/
            const replyElements = document.querySelectorAll(".scomments-reply");
            replyElements.forEach(function (element) {
                element.addEventListener("click", function (e) {
                    e.preventDefault();
                    const text = this.closest('.comments-content').querySelector('.scomments-text').innerHTML.replace(/<blockquote>(.*)<\/blockquote>/gm, '');
                    let text2 = ' ' + text.substring(0, 150) + '...';
                    const link = this.getAttribute('href').replace(/\?num=/g, '#');
                    const description = document.getElementById('description');
                    text2 = text2.replace(/(\r\n|\n|\r|<br>|<br \/>|)/gm, "");
                    description.value = '<blockquote>' + link + text2 + '</blockquote>';
                    window.scrollTo({
                        top: description.offsetTop,
                        behavior: "smooth"
                    });
                });
            });

            /*Группировка отзывов по виду - плохой, хорош. нейтрал.*/
            const checkedCommDivElements = document.querySelectorAll(".checked_comm_div input");
            checkedCommDivElements.forEach(function (element) {
                element.addEventListener("click", function (e) {
                    const votes = document.querySelector("input[name='radio']:checked").value;
                    const objectid = document.querySelector("input[name='object_id']").value;
                    const objectgroup = document.querySelector("input[name='object_group']").value;
                    const all = document.querySelector(".scomments-all");
                    const countGood = document.getElementById('count_good');
                    const countNeutrally = document.getElementById('count_neutrally');
                    const countBad = document.getElementById('count_bad');
                    let linkComment = '';
                    if (votes === 'good') {
                        countGood.style.fontWeight = 'bold';
                        countGood.style.color = '#8af78f';
                        countGood.style.textShadow = 'black 1px 1px 1px, green 0px 0px 0em';
                        countBad.style.fontWeight = '';
                        countBad.style.color = '';
                        countNeutrally.style.fontWeight = '';
                        linkComment = '😀';
                    } else if (votes === 'neutrally') {
                        countNeutrally.style.fontWeight = 'bold';
                        countGood.style.fontWeight = '';
                        countGood.style.textShadow = '';
                        countGood.style.color = '';
                        countBad.style.fontWeight = '';
                        countBad.style.color = '';
                        linkComment = '😐';
                    } else if (votes === 'bad') {
                        countBad.style.fontWeight = 'bold';
                        countBad.style.color = '#f44336';
                        countGood.style.fontWeight = '';
                        countGood.style.textShadow = '';
                        countGood.style.color = '';
                        countNeutrally.style.fontWeight = '';
                        linkComment = '😡';
                    } else {
                        countGood.style.fontWeight = '';
                        countGood.style.color = '';
                        countGood.style.textShadow = '';
                        countNeutrally.style.fontWeight = '';
                        countBad.style.fontWeight = '';
                        countBad.style.color = '';
                        linkComment = '#';
                    }
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/post/comment', true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    const formData = new FormData();
                    formData.append('task', 'votes');
                    formData.append('votes', votes);
                    formData.append('objectid', objectid);
                    formData.append('objectgroup', objectgroup);
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            const data = JSON.parse(xhr.responseText);
                            let str = '';
                            for (let i = 0; i < data.length; i++) {
                                let styleComments, textTitle, status;
                                if (Number(data[i].rate) >= 4) {
                                    styleComments = 'good_comm';
                                    textTitle = 'Хороший отзыв';
                                } else if (Number(data[i].rate) === 3 || Number(data[i].rate) === 0) {
                                    styleComments = 'neutrally_comm';
                                    textTitle = 'Нейтральный отзыв';
                                } else {
                                    styleComments = 'bad_comm';
                                    textTitle = 'Плохой отзыв';
                                }
                                if (Number(data[i].status) === 0) {
                                    status = 'style="background-color: #ffebeb;"';
                                } else {
                                    status = '';
                                }
                                str += '<div class="scomments-item ' + styleComments + '"' + status + '>';
                                if (data[i].registered) {
                                    str += '<div class="comments-avatar-registered" title="' + textTitle + '  зарегистрированного пользователя"></div>';
                                } else {
                                    str += '<div class "comments-avatar-guest" title="' + textTitle + '"></div>';
                                }
                                str += '<div class="comments-content">' +
                                    '<div class="scomments-title">' +
                                    '<span class="scomments-vote">' +
                                    '<a rel="nofollow" href="#" title="Согласен!" class="scomments-vote-good" data-id="' + data[i].id + '" data-value="up">Это правда' + (data[i].isgood ? '<span>' + data[i].isgood + '</span>' : '') + '</a>' +
                                    '<a rel="nofollow" href="#" title="Не согласен!" class="scomments-vote-poor" data-id="' + data[i].id + '" data-value="down">Это ложь' + (data[i].ispoor ? '<span>' + data[i].ispoor + '</span>' : '') + '</a>' +
                                    '</span>' +
                                    '<div>' +
                                    '<a href="#scomment-' + data[i].id + '" name="scomment-' + data[i].id + '" id="scomment-' + data[i].id + '"> ' + linkComment + '</a>';
                                if (data[i].user_name) {
                                    str += '<span class="scomments-user-name" itemprop="author">' + data[i].user_name + '</span>';
                                } else {
                                    str += '<span class="scomments-guest-name" itemprop="author">' + data[i].guest_name + '</span>';
                                }
                                str += '</div></div><div>' +
                                    '<span class="scomments-date" itemprop="datePublished" content="' + data[i].created + '">' + data[i].created + '</span>';
                                if (data[i].country && data[i].country !== 'unknown') {
                                    str += '<span class="scomments-marker"></span><span class="scomments-country">' + data[i].country + '</span>';
                                }
                                str += '</div>' +
                                    '<div class="scomments-text" itemprop="reviewBody">' + data[i].description + '</div>';
                                if (Number(data[i].mages) > 0) {
                                    str += '<a href="#" data-id="' + data[i].id + '" class="scomments-item-images-toogle">Показать прикрепленное фото</a>' +
                                        '<div class="scomments-item-images"></div>';
                                }
                                str += '</div></div>';
                            }
                            document.querySelector("div.pagination").innerHTML = '';
                            all.innerHTML = str;
                        }
                    };
                    xhr.send(formData);
                });
            });
        },
        form: function () {
            document.addEventListener("blur", function (e) {
                if (e.target.id === "email") {
                    document.querySelector("label[for='email']").style.display = 'none';
                }
            });
            document.addEventListener("focus", function (e) {
                if (e.target.id === "email") {
                    document.querySelector("label[for='email']").style.display = 'block';
                }
            });

            document.addEventListener("click", function (e) {
                if (e.target.id === "submit") {
                    e.preventDefault();
                    e.stopPropagation();
                    document.querySelector('#myform').submit();
                }
            });

            document.addEventListener("change", function (e) {
                if (e.target.closest('#upload') && e.target.type === "file") {
                    e.preventDefault();
                    e.stopPropagation();
                    const fileInput = document.getElementById("file");
                    const slider = document.getElementById("slider");
                    const msg = document.getElementById("msg");
                    const attachInput = document.querySelector('input[name="attach"]');
                    const uploadForm = new FormData();
                    uploadForm.append("file", fileInput.files[0]);
                    uploadForm.append('task', 'addImage');
                    uploadForm.append('attach', attachInput.value);
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "/post/comment", true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.upload.addEventListener("progress", function (e) {
                        if (e.lengthComputable) {
                            const percent = (e.loaded / e.total) * 100;
                            document.getElementById("percent").innerHTML = percent.toFixed(0) + "%";
                        }
                    });

                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 1) {
                                // Создаем элемент карусели и добавляем его внутрь #slider
                                const newSlide = document.createElement("div");
                                newSlide.className = "row-slide";
                                newSlide.innerHTML = '<a href="#" data-id="' + response.id + '" data-attach="' + response.attach + '" class="remove-slide"></a><img src="/images/comments/' + response.thumb + '">';
                                slider.appendChild(newSlide);
                            } else if (response.status === 2) {
                                msg.className = "msg-error";
                                msg.innerHTML = response.msg;
                                msg.style.display = "block";
                            }
                            // Очищаем форму и скрываем индикатор процента
                            document.getElementById("upload").reset();
                            document.getElementById("percent").style.display = "none";
                        }
                    };

                    xhr.send(uploadForm);
                }
            });

            /*отправка нового отзыва*/
                const submitButton = document.querySelector('#submit');
                const form = document.querySelector('#myform');
                if (submitButton && form) {
                    submitButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('click2');
                        const data = new FormData(form);
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '/post/comment', true);
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState === 4) {
                                document.querySelector('#msg').style.display = 'none';
                                document.querySelector('#loader').innerHTML = '<img src="/images/loader.gif">';
                                document.querySelector('#loader').style.display = 'block';
                            }
                        };
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                const data = JSON.parse(xhr.responseText);
                                if (data.status === 1) {
                                    const msg = document.querySelector('#msg');
                                    msg.className = 'msg-success';
                                    msg.innerHTML = data.msg;
                                    msg.style.display = 'block';
                                    form.reset();
                                    form.style.display = 'none';
                                    document.querySelector('#loader').style.display = 'none';
                                }
                                if (data.status === 2) {
                                    const msg = document.querySelector('#msg');
                                    msg.className = 'msg-error';
                                    msg.innerHTML = data.msg;
                                    msg.style.display = 'block';
                                    document.querySelector('#loader').style.display = 'none';
                                }
                                _private.scroll();
                            }
                        };
                        xhr.send(data);
                    });
                }

            /*удаление фото из отзыва*/
            document.addEventListener("click", function (e) {
                if (e.target.classList.contains("remove-slide")) {
                    e.preventDefault();
                    document.querySelector('#msg').style.display = 'none';
                    const parentDiv = e.target.parentNode;
                    const id_img = e.target.getAttribute("data-id");
                    const attach = e.target.getAttribute("data-attach");
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/post/comment', true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    const formData = new FormData();
                    formData.append('task', 'removeImage');
                    formData.append('id_img', id_img);
                    formData.append('attach', attach);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4) {
                            document.querySelector('#percent').innerHTML = '';
                            document.querySelector('#percent').style.display = 'block';
                        }
                    };
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            const data = JSON.parse(xhr.responseText);
                            if (data.status === 1) {
                                parentDiv.remove();
                            }
                            if (data.status === 2) {
                                const msg = document.querySelector('#msg');
                                msg.className = 'msg-error';
                                msg.innerHTML = data.msg;
                                msg.style.display = 'block';
                            }
                            document.querySelector('#percent').innerHTML = '';
                            document.querySelector('#percent').style.display = 'none';
                        }
                    };
                    xhr.send(formData);
                }
            });
        },
        scroll: function (callback) {
            const scrollTo = document.querySelector(".scomments-anchor").offsetTop;
            window.scrollTo({
                top: scrollTo,
                behavior: "smooth"
            });
            if (callback) {
                callback();
            }
        }
    };

    return {
        init: function () {
            _private.list();
            _private.form();
        }
    };
})();

document.addEventListener("DOMContentLoaded", function () {
    comments.init();
});
