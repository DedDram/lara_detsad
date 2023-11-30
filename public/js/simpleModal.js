/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************************!*\
  !*** ./resources/js/simpleModal.js ***!
  \*************************************/
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('click', function (e) {
    if (e.target.closest('.simplemodal')) {
      e.preventDefault();
      var url = e.target.getAttribute('href');
      var width = e.target.getAttribute('data-width') + 'px';
      var height = e.target.getAttribute('data-height') + 'px';
      var widthModal;
      if (!url) {
        var linkElement = e.target.closest('a');
        if (linkElement) {
          url = linkElement.getAttribute('href');
          width = linkElement.getAttribute('data-width') + 'px';
          height = linkElement.getAttribute('data-height') + 'px';
        }
      }
      var win_width = window.innerWidth > 0 ? window.innerWidth : screen.width;
      if (win_width < 769) {
        width = height = '95%';
        widthModal = 'width: 95%;height: 50%;';
      }
      var modal = document.createElement('div');
      modal.classList.add('modal');
      modal.innerHTML = "<iframe src=\"".concat(url, "\" style=\"border: 0; width: ").concat(width, "; height: ").concat(height, ";\"></iframe>");
      document.body.appendChild(modal);
      modal.style.cssText = "position: fixed; z-index: 9999; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; border: 1px solid black; box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2); ".concat(widthModal);
      var closeButton = document.createElement('div');
      closeButton.classList.add('close-button');
      closeButton.style.cssText = 'background: url("/images/x.png") no-repeat; width: 25px; height: 29px; display: inline; z-index: 3200; position: absolute; top: -15px; right: -16px; cursor: pointer;';
      modal.appendChild(closeButton);
      var overlay = document.createElement('div');
      overlay.classList.add('modal-overlay');
      overlay.style.cssText = 'width: 100%; height: 100%; position: fixed; top: 0; left: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 9998;';
      document.body.appendChild(overlay);
    } else if (e.target.classList.contains('modal')) {
      e.target.remove();
      document.querySelector('.modal-overlay').remove();
    } else if (e.target.classList.contains('close-button')) {
      document.querySelector('.modal').remove();
      document.querySelector('.modal-overlay').remove();
    } else if (e.target.classList.contains('modal-overlay')) {
      document.querySelector('.modal').remove();
      e.target.remove();
    }
  });
});
/******/ })()
;