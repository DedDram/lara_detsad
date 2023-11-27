document.addEventListener('DOMContentLoaded', function () {

    // Функция для отображения данных на странице
    function displayItems(items) {
        const tableBody = document.getElementById('cattable').getElementsByTagName('tbody')[0];
        tableBody.innerHTML = ''; // Очистить таблицу

        // Сортируем данные по полю 'okrug'
        items.sort((a, b) => {
            if (a.okrug < b.okrug) return -1;
            if (a.okrug > b.okrug) return 1;
            return 0;
        });

        let lastGroup = null;

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

            // Добавляем строку группировки, если значение 'okrug' изменилось
            if (item.okrug !== lastGroup) {
                const groupRow = document.createElement('tr');
                groupRow.className = 'group';
                const groupCell = document.createElement('td');
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
