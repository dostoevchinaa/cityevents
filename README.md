##1. Общая информация
- CMS	WordPress 6.7
- Хостинг	Timeweb (платный)
- Домен	https://ck482085-wordpress-9w0kp.tw1.ru
- Логин: admin
- Пароль: O5dYL$kZk78T
- 3djf Orth pjQi bQGl gjG6 yU1L
- Затраченное время	~12 часов (с учётом отладки и переноса с Beget)

##2. Реализованный функционал

###2.1. Тип записи "Событие": пункт меню "События" в админ-панели, поддержка произвольных полей, ЧПУ: /events/название-события/

###2.2. Мета-поля:
- place (место проведения, строка)
- start_at (дата и время начала, формат YYYY-MM-DD HH:MM)
- end_at (дата и время окончания)
- tags (массив строк, не больше 5, хранится как JSON)
- capacity (количество мест, целое число 1-5000)
- status (статус события: draft/published/cancelled)
- popularity (число 1-5, рассчитывается автоматически)
- change_number (счётчик изменений)

###2.3. Расчёт popularity

days_to_start = start_date - today_date
raw = 3 + (capacity / 1000) - (days_to_start / 10) + количество_тегов
popularity = raw, приведённый к диапазону 1..5

При popularity = 1 создание события блокируется с ошибкой 400.

###2.5. Сортировка (GET /events)

###2.6. Валидация данных

###2.7. Авторизация

##3.Примеры запросов (curl)

###3.1.GET /events

curl -X GET https://ck482085-wordpress-9w0kp.tw1.ru/wp-json/events/v1/events

###3.2.GET /events/{id}

curl -X GET https://ck482085-wordpress-9w0kp.tw1.ru/wp-json/events/v1/events/16

###3.3. POST /events

curl -X POST https://ck482085-wordpress-9w0kp.tw1.ru/wp-json/events/v1/events -H "Content-Type: application/json" -u admin:3djfOrthpjQibQGlgjG6yU1L -d "{\"title\":\"Новое событие\",\"place\":\"Казань\",\"start_at\":\"2026-04-01 19:00\",\"end_at\":\"2026-04-01 22:00\",\"tags\":[\"концерт\"],\"capacity\":1000,\"status\":\"publish\"}"

###3.4.PUT /events/{id}

curl -X PUT https://ck482085-wordpress-9w0kp.tw1.ru/wp-json/events/v1/events/23 -H "Content-Type: application/json" -u admin:3djfOrthpjQibQGlgjG6yU1L -d "{\"title\":\"Обновлённое название\",\"capacity\":1500}"

###3.5.DELETE /events/{id}

curl -X DELETE https://ck482085-wordpress-9w0kp.tw1.ru/wp-json/events/v1/events/23 -H "Content-Type: application/json" -u admin:3djfOrthpjQibQGlgjG6yU1L
