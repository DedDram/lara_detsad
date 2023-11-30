/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************!*\
  !*** ./resources/js/comments.js ***!
  \**********************************/
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var comments = function () {
  var _private = {
    list: function list() {
      /*Показ фото отзыва*/
      var imagesToogleElements = document.querySelectorAll(".scomments-item-images-toogle");
      imagesToogleElements.forEach(function (element) {
        element.addEventListener("click", function (e) {
          e.preventDefault();
          var id = this.getAttribute("data-id");
          var el = this.parentNode.querySelector(".scomments-item-images");
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '/post/comment', true);
          xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
          var formData = new FormData();
          formData.append('task', 'images');
          formData.append('id', id);
          xhr.onload = function () {
            if (xhr.status === 200) {
              el.innerHTML = '';
              var rows = JSON.parse(xhr.responseText);
              rows.forEach(function (row) {
                var a = document.createElement('a');
                a.href = '/images/comments/' + row.original;
                a.className = 'simplemodal';
                a.setAttribute('data-width', '800');
                a.setAttribute('data-height', '500');
                a.innerHTML = '<img src="/images/comments/' + row.thumb + '">';
                el.appendChild(a);
              });
              if (el.style.display === "none" || el.style.display === "") {
                el.style.display = "block";
              } else {
                el.style.display = "none";
              }
            }
          };
          xhr.send(formData);
        });
      });

      /*голосование за отзыв*/
      var voteElements = document.querySelectorAll(".scomments-vote a");
      voteElements.forEach(function (element) {
        element.addEventListener("click", function (e) {
          e.preventDefault();
          var el = this.parentNode;
          var id = this.getAttribute("data-id");
          var value = this.getAttribute("data-value");
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '/post/comment', true);
          xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
          var formData = new FormData();
          formData.append('task', 'vote');
          formData.append('value', value);
          formData.append('id', id);
          xhr.onload = function () {
            if (xhr.status === 200) {
              var data = JSON.parse(xhr.responseText);
              el.innerHTML = data.msg;
            }
          };
          xhr.send(formData);
        });
      });

      /* Ответить на отзыв*/
      var replyElements = document.querySelectorAll(".scomments-reply");
      replyElements.forEach(function (element) {
        element.addEventListener("click", function (e) {
          e.preventDefault();
          var text = this.closest('.comments-content').querySelector('.scomments-text').innerHTML.replace(/<blockquote>(.*)<\/blockquote>/gm, '');
          var text2 = ' ' + text.substring(0, 150) + '...';
          var link = this.getAttribute('href').replace(/\?num=/g, '#');
          var description = document.getElementById('description');
          text2 = text2.replace(/(\r\n|\n|\r|<br>|<br \/>|)/gm, "");
          description.value = '<blockquote>' + link + text2 + '</blockquote>';
          window.scrollTo({
            top: description.offsetTop,
            behavior: "smooth"
          });
        });
      });

      /*Группировка отзывов по виду - плохой, хорош. нейтрал.*/
      var checkedCommDivElements = document.querySelectorAll(".checked_comm_div input");
      checkedCommDivElements.forEach(function (element) {
        element.addEventListener("click", function (e) {
          var votes = document.querySelector("input[name='radio']:checked").value;
          var objectid = document.querySelector("input[name='object_id']").value;
          var objectgroup = document.querySelector("input[name='object_group']").value;
          var all = document.querySelector(".scomments-all");
          var countGood = document.getElementById('count_good');
          var countNeutrally = document.getElementById('count_neutrally');
          var countBad = document.getElementById('count_bad');
          var linkComment = '';
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
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '/post/comment', true);
          xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
          var formData = new FormData();
          formData.append('task', 'votes');
          formData.append('votes', votes);
          formData.append('objectid', objectid);
          formData.append('objectgroup', objectgroup);
          xhr.onload = function () {
            if (xhr.status === 200) {
              var data = JSON.parse(xhr.responseText);
              var str = '';
              for (var i = 0; i < data.length; i++) {
                var styleComments = void 0,
                  textTitle = void 0,
                  status = void 0;
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
                str += '<div class="comments-content">' + '<div class="scomments-title">' + '<span class="scomments-vote">' + '<a rel="nofollow" href="#" title="Согласен!" class="scomments-vote-good" data-id="' + data[i].id + '" data-value="up">Это правда' + (data[i].isgood ? '<span>' + data[i].isgood + '</span>' : '') + '</a>' + '<a rel="nofollow" href="#" title="Не согласен!" class="scomments-vote-poor" data-id="' + data[i].id + '" data-value="down">Это ложь' + (data[i].ispoor ? '<span>' + data[i].ispoor + '</span>' : '') + '</a>' + '</span>' + '<div>' + '<a href="#scomment-' + data[i].id + '" name="scomment-' + data[i].id + '" id="scomment-' + data[i].id + '"> ' + linkComment + '</a>';
                if (data[i].user_name) {
                  str += '<span class="scomments-user-name" itemprop="author">' + data[i].user_name + '</span>';
                } else {
                  str += '<span class="scomments-guest-name" itemprop="author">' + data[i].guest_name + '</span>';
                }
                str += '</div></div><div>' + '<span class="scomments-date" itemprop="datePublished" content="' + data[i].created + '">' + data[i].created + '</span>';
                if (data[i].country && data[i].country !== 'unknown') {
                  str += '<span class="scomments-marker"></span><span class="scomments-country">' + data[i].country + '</span>';
                }
                str += '</div>' + '<div class="scomments-text" itemprop="reviewBody">' + data[i].description + '</div>';
                if (Number(data[i].mages) > 0) {
                  str += '<a href="#" data-id="' + data[i].id + '" class="scomments-item-images-toogle">Показать прикрепленное фото</a>' + '<div class="scomments-item-images"></div>';
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
    form: function form() {
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
          var fileInput = document.getElementById("file");
          var slider = document.getElementById("slider");
          var msg = document.getElementById("msg");
          var attachInput = document.querySelector('input[name="attach"]');
          var uploadForm = new FormData();
          uploadForm.append("file", fileInput.files[0]);
          uploadForm.append('task', 'addImage');
          uploadForm.append('attach', attachInput.value);
          var xhr = new XMLHttpRequest();
          xhr.open("POST", "/post/comment", true);
          xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
          document.querySelector('#loader').innerHTML = '<img src="/images/loader.gif">';
          document.querySelector('#loader').style.display = 'block';
          xhr.onload = function () {
            if (xhr.status === 200) {
              var response = JSON.parse(xhr.responseText);
              if (response.status === 1) {
                // Создаем элемент карусели и добавляем его внутрь #slider
                var newSlide = document.createElement("div");
                newSlide.className = "row-slide";
                newSlide.innerHTML = '<a href="#" data-id="' + response.id + '" data-attach="' + response.attach + '" class="remove-slide"></a><img src="/images/comments/' + response.thumb + '">';
                slider.appendChild(newSlide);
              } else if (response.status === 2) {
                msg.className = "msg-error";
                msg.innerHTML = response.msg;
                msg.style.display = "block";
              }
              // Очищаем форму и скрываем loader
              document.getElementById("upload").reset();
              document.querySelector('#loader').style.display = "none";
            }
          };
          xhr.send(uploadForm);
        }
      });

      /*отправка нового отзыва*/
      var submitButton = document.querySelector('#submit');
      var form = document.querySelector('#myform');
      if (submitButton && form) {
        submitButton.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          var data = new FormData(form);
          var xhr = new XMLHttpRequest();
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
              var _data = JSON.parse(xhr.responseText);
              if (_data.status === 1) {
                var msg = document.querySelector('#msg');
                msg.className = 'msg-success';
                msg.innerHTML = _data.msg;
                msg.style.display = 'block';
                form.reset();
                form.style.display = 'none';
                document.querySelector('#slider').style.display = 'none';
                document.querySelector('#upload').style.display = 'none';
              }
              if (_data.status === 2) {
                var _msg = document.querySelector('#msg');
                _msg.className = 'msg-error';
                _msg.innerHTML = _data.msg;
                _msg.style.display = 'block';
              }
              document.querySelector('#loader').style.display = 'none';
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
          var parentDiv = e.target.parentNode;
          var id_img = e.target.getAttribute("data-id");
          var attach = e.target.getAttribute("data-attach");
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '/post/comment', true);
          xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
          var formData = new FormData();
          formData.append('task', 'removeImage');
          formData.append('id_img', id_img);
          xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
              document.querySelector('#percent').innerHTML = '';
              document.querySelector('#percent').style.display = 'block';
            }
          };
          xhr.onload = function () {
            if (xhr.status === 200) {
              var data = JSON.parse(xhr.responseText);
              if (data.status === 1) {
                parentDiv.remove();
              }
              if (data.status === 2) {
                var msg = document.querySelector('#msg');
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
    scroll: function scroll(callback) {
      var scrollTo = document.querySelector(".scomments-anchor").offsetTop;
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
    init: function init() {
      _private.list();
      _private.form();
    }
  };
}();
document.addEventListener("DOMContentLoaded", function () {
  comments.init();
});
/******/ })()
;