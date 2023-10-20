document.addEventListener('DOMContentLoaded', function () {

    // Функция для отображения данных на странице
    function displayItems(items) {
        const tableBody = document.getElementById('cattable').getElementsByTagName('tbody')[0];
        tableBody.innerHTML = ''; // Очистить таблицу

        items.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = index % 2 === 0 ? 'sectiontableentry even' : 'sectiontableentry odd';

            const numCell = document.createElement('td');
            numCell.textContent = index + 1;
            row.appendChild(numCell);

            const nameCell = document.createElement('td');
            const nameLink = document.createElement('a');
            nameLink.href = item.link;
            nameLink.textContent = item.name;
            nameCell.appendChild(nameLink);
            row.appendChild(nameCell);

            const ratingCell = document.createElement('td');
            ratingCell.textContent = parseFloat(item.average).toFixed(1);
            row.appendChild(ratingCell);

            const commentsCell = document.createElement('td');
            commentsCell.textContent = item.comments;
            row.appendChild(commentsCell);

            const okrugCell = document.createElement('td');
            okrugCell.innerHTML = '<h3>' + item.okrug + '</h3';
            row.appendChild(okrugCell);

            tableBody.appendChild(row);
        });
    }

    // Функция для обработки события изменения поискового запроса
    function handleSearch() {
        const searchQuery = document.getElementById('searchQuery').value.toLowerCase().trim();
        const filteredItems = items.filter(item => {
            return (
                item.name.toLowerCase().includes(searchQuery) ||
                item.n.toString().includes(searchQuery)
            );
        });

        displayItems(filteredItems);
    }

    // Добавить обработчик события на изменение поискового запроса
    document.getElementById('searchQuery').addEventListener('input', handleSearch);

    // Изначально отобразить все элементы
    displayItems(items);
});
