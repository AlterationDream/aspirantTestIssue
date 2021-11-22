<h1 align="center">Тестовое задание</h2>
<h2 align="center" style="margin-top:-24px">Таракановского Валерия Александровича</h3>

#
#### Подготовка к запуску приложения:
- Переместите файлы проекта в рабочую директорию, где он будет разворачиваться.
- В корневой директории проекта выполните консольную команду **composer install**, 
  чтобы необходимые зависимости были установлены.
- Найдите файл **.env.example** и [скопируйте и] переименуйте его в **.env**.
- Создайте базу данных на сервере в кодировке **utf8_general_ci** и введите её 
  название в файле .env в строчке **DB_DATABASE**.
- Также введите логин и пароль в строчках **DB_USERNAME** и **DB_PASSWORD** соответственно.
- Запустите команду **php artisan key:generate**, чтобы сгенерировать уникальный ключ приложения.
- Также запустите **php artisan migrate**, 
  чтобы создать таблицы, необходимые для работы приложения.
- И после запустите **php artisan serve**, чтобы начать его работу.
- Введите в консоли команду "php artisan posts:import -h" и ознакомьтесь с её описанием. 
  Она импортирует необходимое количество трейлеров из RSS канала iTunes Movie Trailers, 
  которые сразу будут отображены на главной странице, либо ответ командной строки 
  ознакомит Вас с возникшей ошибкой. 
  
#
#### Проделанная работа:

- Сперва я открыл предоставленную мне ссылку на репозиторий с тестовым заданием 
  и ознакомился с поставленной задачей.
- Вскоре я обнаружил, что оно написано на Symphony, а не на Laravel, что ввело меня 
  в небольшое заблуждение. Я решил не дожидаться ответа от HR-менеджера и решил 
  развернуть чистую установку Laravel. Тем самым я не выполнял ту часть задания,
  в которой мне необходимо было исправить ошибки в существующем коде. Лучшие
  ошибки — это те, которых нет.
- Первым делом я выполнил задания уровня Junior, что не заняло много времени. 
  Я решил сделать сменяющееся время, хоть это и не было указано в задании, 
  так как особо ничем не занимался. Названия контроллера и его метода тоже 
  не составили трудности.
- Дальше я чуть лучше ознакомился с заданием и переосмыслил необходимое время на его 
  решение, подумал над тем как лучше всего реализовать отметки *Мне нравится*. 
  Остановился на связи пользователей и постов через отдельную промежуточную таблицу,
  каждая запись в которой будет равняться одному лайку.
- Также я проанализировал RSS канал iTunes, чтобы представить структуру таблицы трейлеров,
  выделил ключевые элементы, которые необходимо будет хранить в базе. Для главного
  изображения трейлера, я решил взять "фоновое" изображение, а не постер, потому
  как последнее было слишком уж маленького разрешения, чтобы хоть что-то на нём 
  разглядеть.
- Создал соответствующие миграции для базы данных, чтобы свежее развёрнутое приложение
  понимало где и какие оно хранит данные.
- Далее я создал модель постов, которая представляет собой идею об этих трейлерах,
  и в ней определил взаимосвязь с моделью пользователей. То же проделал и для, собственно,
  модели пользователей. Затем я определил контроллер, которому сразу же поручил задачу
  выдачи необходимого представления главной страницы. В дальнейшем он будет заниматься
  импортом записей и обработкой лайков.
- В файле /routes/web.php указал, что при запросе к сайту, он должен обращаться к 
  контроллеру постов, чтобы тот сделал своё дело.
- Я недолго думал в выборе решения для получения данных о трейлерах: сразу нашёл
  соответствующий инструмент на GitHub'е и подключил его с помощью composer'а. Теперь 
  осталось лишь с помощью магического метода обратиться к каналу и получить массив данных. 
- Мне не приходилось писать программные тесты для приложений, не выдавалось такого реального 
  шанса, поэтому я сделал так, как это делал всегда — постарался покрыть все основные 
  ошибки и edge case'ы валидацией успешности и при ошибке выдавать соответствующее сообщение. 
  Я предусмотрел возникновение ошибки при обращении к RSS каналу (отсутствие подключения), 
  смену формата данных в будущем, возникновение ошибок при внесении записей в базу данных. 
  На последний случай я решил добавить откат добавленных операцией записей. Если не удаётся даже
  он, то возвращать код ошибки, потому что тогда ясно, что в базе данных внезапно произошёл сбой 
  и стоит его отследить.
- Для меня было в новинку обращение к методу контроллера через консоль и это показалось
  довольно интересной задачей, так что я даже удосужился написать для неё краткое описание.
- После того как записи успешно загружались в базу данных, я приступил к выводу их 
  на главную станицу. Отправил их в переменной в файл представления и в цикле 
  отрисовал каждый элемент, добавив ссылку на детальную запись.
- В файле роутов я добавил получение id трейлера из адресной строки, роуты логина, регистрации
  и выхода из аккаунта пользователя, создал сопутствующие файлы представлений.
- Контроллер пользователей пополнил соответствующими методами и написал валидацию полей, 
  аутентификацию пользователя, **зашифровал пароль**, для хранения в базе данных.
- К детальной странице я приступил в последнюю очередь, чтобы сразу реализовать кнопку *Мне нравится*,
  когда пользователи уже могут регистрироваться и возможно протестировать её работоспособность. 
- Картинки лайков стянул со стоков, обрезал, поменял цвет. Написал для них метод в контроллере 
  постов, в котором проверял выполнен ли вход и существует ли пост, на который был поставлен лайк. 
- В нём же сменял статус лайка и получал их итоговое количество, изменяя число на странице, без её
  обновления.
- Ну и финальным штрихом нашёл несколько ошибок, пока писал это подробное пояснение, которые добавлю
  тем же коммитом, что и этот файл.
