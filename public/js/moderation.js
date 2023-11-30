/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************!*\
  !*** ./resources/js/moderation.js ***!
  \************************************/
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
document.addEventListener("DOMContentLoaded", function () {
  function handleCommentAction(e) {
    e.preventDefault();
    var el = this.parentNode.querySelector(".scomments-control-msg");
    var task = this.getAttribute("data-task");
    var object_group = this.getAttribute("data-object-group");
    var object_id = this.getAttribute("data-object-id");
    var item_id = this.getAttribute("data-item-id");
    el.innerHTML = '<img src="/images/loader.gif">';
    el.style.display = "block";
    var formData = new FormData();
    formData.append('task', task);
    formData.append('object_group', object_group);
    formData.append('object_id', object_id);
    formData.append('item_id', item_id);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/post/comment', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.onload = function () {
      if (xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);
        el.innerHTML = response.msg;
      }
    };
    xhr.send(formData);
  }
  function handleEditComment(e) {
    e.preventDefault();
    var text = this.closest('.comments-content').querySelector('.scomments-text').innerHTML;
    var msg = this.parentNode.querySelector('.scomments-control-msg');
    var form = document.querySelector('.scomments-form');
    var item_id = this.getAttribute("data-item-id");
    var description = document.getElementById('description');
    var task1 = document.getElementById('task1');
    var task2 = document.getElementById('task2');
    var item_id_form = document.querySelector('#myform input[name="item_id"]');
    description.value = text;
    var el = document.getElementById('slider');
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/post/comment', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    form.querySelector('header').textContent = "Редактировать отзыв";
    task1.value = 'edit';
    task2.value = 'add';
    item_id_form.value = item_id;
    var formData = new FormData();
    formData.append('task', 'images');
    formData.append('id', item_id);
    xhr.onload = function () {
      if (xhr.status === 200) {
        var rows = JSON.parse(xhr.responseText);
        msg.innerHTML = rows.msg;
        rows.forEach(function (row) {
          var div = document.createElement('div');
          div.className = 'row-slide';
          div.innerHTML = '<a href="#" data-id="' + row.id + '" data-attach="' + row.attach + '" class="remove-slide"></a><img src="/images/comments/' + row.thumb + '">';
          el.appendChild(div);
        });
      }
    };
    xhr.send(formData);
    window.scrollTo({
      top: description.offsetTop,
      behavior: "smooth"
    });
  }
  var commentActions = document.querySelectorAll(".scomments-control-delete, .scomments-control-publish, .scomments-control-unpublish, .scomments-control-blacklist");
  commentActions.forEach(function (action) {
    action.addEventListener("click", handleCommentAction);
  });
  var editCommentButtons = document.querySelectorAll(".scomments-control-edit");
  editCommentButtons.forEach(function (button) {
    button.addEventListener("click", handleEditComment);
  });
});
/******/ })()
;