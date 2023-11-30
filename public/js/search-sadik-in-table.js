/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************************!*\
  !*** ./resources/js/search-sadik-in-table.js ***!
  \***********************************************/
document.addEventListener('DOMContentLoaded', function () {
  // Функция для отображения данных на странице
  function displayItems(items) {
    var tableBody = document.getElementById('cattable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = ''; // Очистить таблицу
    if (items[0] && items[0].okrug !== undefined) {
      // Сортируем данные по полю 'okrug'
      items.sort(function (a, b) {
        if (a.okrug < b.okrug) return -1;
        if (a.okrug > b.okrug) return 1;
        return 0;
      });
    }
    var lastGroup = null;
    items.forEach(function (item, index) {
      var row = document.createElement('tr');
      row.className = index % 2 === 0 ? 'sectiontableentry even' : 'sectiontableentry odd';
      var numCell = document.createElement('td');
      numCell.textContent = index + 1;
      row.appendChild(numCell);
      var nameCell = document.createElement('td');
      var nameLink = document.createElement('a');
      nameLink.href = item.link;
      nameLink.textContent = item.name;
      nameCell.appendChild(nameLink);
      row.appendChild(nameCell);
      var ratingCell = document.createElement('td');
      ratingCell.textContent = parseFloat(item.average).toFixed(1);
      row.appendChild(ratingCell);
      var commentsCell = document.createElement('td');
      commentsCell.textContent = item.comments;
      row.appendChild(commentsCell);

      // Добавляем строку группировки, если значение 'okrug' изменилось
      if (item.okrug !== undefined && item.okrug !== lastGroup) {
        var groupRow = document.createElement('tr');
        groupRow.className = 'group';
        var groupCell = document.createElement('td');
        groupCell.colSpan = 4; // Количество столбцов в таблице
        groupCell.innerHTML = '<h3>' + item.okrug + '</h3>';
        groupRow.appendChild(groupCell);
        tableBody.appendChild(groupRow);
        lastGroup = item.okrug;
      }
      tableBody.appendChild(row);
    });
  }

  // Функция для обработки события изменения поискового запроса
  function handleSearch() {
    var searchQuery = document.getElementById('searchQuery').value.toLowerCase().trim();
    var filteredItems = items.filter(function (item) {
      return item.name.toLowerCase().includes(searchQuery) || item.n.toString().includes(searchQuery);
    });
    displayItems(filteredItems);
  }

  // Добавить обработчик события на изменение поискового запроса
  document.getElementById('searchQuery').addEventListener('input', handleSearch);

  // Изначально отобразить все элементы
  displayItems(items);
});
/******/ })()
;