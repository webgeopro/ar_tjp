$('document').ready(function(){

    var searchPage; // Input для хранения номера текущей страницы
    /**
     * Обработка клика по кнопки Next
     * Добавляет 1 и присваивает input-у с name=page [id=inpSearchPage]
     * Отправка формы
     */
    $("#btnSearchNext").click(function(){
        //todo Проверка диапазона: cnt < limit === последняя страница
        searchPage = parseInt($("input#page").val()) + 1;
        $("input#page").val(searchPage);
        formSearchBlock.submit();
    });

    /**
     * Обработка клика по кнопки Prev
     * Минусует 1 и присваивает input-у с name=page [id=inpSearchPage]
     * Проверяет если отрицательное значение
     * Отправка формы
     */
    $("#btnSearchPrev").click(function(){
        searchPage = parseInt($("input#page").val()) - 1;
        if (0 >= searchPage)
            $("input#page").val('0');
        else
            $("input#page").val(searchPage);
        //alert($("input#page").val());
        formSearchBlock.submit();
    });

    /**
     * Клик по кнопке отправки формы
     * @return boolean [true - отправка формы]
     */
    $("#searchButton").click(function(){
        $("#searchCriteria").blur(); // Убираем фокус c поля ввода [Нужно для отработки нажатия Enter]

        var val = $("#searchCriteria").val().trim(); // Содержимое поля ввода
        var bool = $("#searchCriteria").hasClass('blur'); // Класс накладывается в плагине jquery.hint

        if (val.length && bool) return false; // Если есть класс blur, значит в input-е значение по умолчанию
        //return val.length; // Если поле не пустое - отправляем форму
    });

    /**
     * Накладываем плагин jquery.hint на поле ввода и обработчик нажатия кнопки Enter.
     * Плагин отображает в input-e значение по умолчанию, исчезающее при клике
     */
    $("#searchCriteria") // ID input-а поиска
        .hint()  // Скрывать начальное содержимое строки поиска при клике
        .keyup(function (e) { // Отрабатываем отправку формы при нажатии Enter
            if (e.keyCode == 13) {
                $("#searchButton").click();
            }
        });

});

/*var search_SearchBlock_promptString, search_SearchBlock_input,
    search_SearchBlock_errorString, search_SearchBlock_inProgressString;
var search_submitted = false;

function search_SearchBlock_init(prompt, error, inProgress) {
    search_SearchBlock_promptString = prompt;
    search_SearchBlock_errorString = error;
    search_SearchBlock_inProgressString = inProgress;
    search_SearchBlock_input = document.getElementById('search_SearchBlock').searchCriteria;

    search_SearchBlock_input.value = prompt;
}

function search_SearchBlock_checkForm() {
    var sc = search_SearchBlock_input.value;
    if (search_submitted) {
	alert(search_SearchBlock_inProgressString);
	return false;
    } else if (sc == search_SearchBlock_promptString || sc == '') {
	alert(search_SearchBlock_errorString);
	return false;
    }
    document.getElementById('search_SearchBlock').submit();
    search_submitted = true;
    return true;
}

function search_SearchBlock_focus() {
    if (search_SearchBlock_input.value == search_SearchBlock_promptString) {
	search_SearchBlock_input.value = '';
    }
}

function search_SearchBlock_blur() {
    if (search_SearchBlock_input.value == '') {
	search_SearchBlock_input.value = search_SearchBlock_promptString;
    }
}*/
