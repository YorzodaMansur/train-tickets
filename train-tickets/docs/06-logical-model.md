Модели мантиқии пойгоҳи додаҳо: train_ticket
1. Маълумоти умумӣ
Лоиҳа барои ҷустуҷӯи ҳаракати қатораҳои байнишаҳрӣ, интихоби хатсайр, қатора, сана ва ҷой, инчунин фармоиш додани чипта пешбинӣ шудааст.
2. Ҷадвали ибтидоӣ (ғайринормалӣ)
train_number	train_name	route_name	intermediate_stations	departure_datetime	passenger_name	seats	price
TR-101	Сомон	Душанбе–Хуҷанд	Айнӣ, Шаҳристон	2026-12-12 08:00	Алиев Фарҳод	01, 02	250
TR-102	Истаравшан	Душанбе–Истаравшан	Ваҳдат, Файзобод	2026-12-13 09:00	Каримова Мадина	05	180
TR-101	Сомон	Душанбе–Хуҷанд	Айнӣ, Шаҳристон	2026-12-14 08:00	Раҳимов Ҷамшед	03	250


Ин ҷадвал ғайринормалӣ аст, зеро intermediate_stations ва seats метавонанд якчанд қимат дошта бошанд. Ҳамчунин маълумоти қатора ва хатсайр дар сатрҳои фармоиш такрор мешавад.
3. Нормализатсия
3.1. 1ШН
Вайронкунӣ: intermediate_stations ва seats рӯйхати қиматҳоро дар як ҳуҷайра нигоҳ медоранд.

Ислоҳ: ҳар як истгоҳ ва ҳар як ҷой ҳамчун сабти алоҳида нигоҳ дошта мешавад. Истгоҳҳои хатсайр дар route_stations, ҷойҳои вагон дар seats ва фармоишҳо дар bookings нигоҳ дошта мешаванд.
3.2. 2ШН
Вайронкунӣ: дар маълумоти пайвасткунандаи хатсайр–истгоҳ, номи истгоҳ танҳо аз station_id вобаста аст, на аз тамоми ҷуфти route_id ва station_id. Маълумоти қатора низ мустақил аз фармоиш мебошад.

Ислоҳ: маълумоти истгоҳҳо ба stations, қатораҳо ба trains ва пайвастшавии онҳо ба route_stations ҷудо карда мешавад. Барои ҷадвалҳои асосӣ калиди суррогатии id истифода мешавад.
3.3. 3ШН
Вайронкунӣ ва ислоҳ:
Вобастагии транзитивӣ	Ислоҳ
bookings → trips → trains → train_number, train_name	Маълумоти қатора танҳо дар trains нигоҳ дошта мешавад
bookings → trips → routes → route_name	Маълумоти хатсайр танҳо дар routes нигоҳ дошта мешавад
route_stations → stations → station_name, city	Маълумоти истгоҳ танҳо дар stations нигоҳ дошта мешавад


Натиҷа: ҳар як ҷадвал маълумоти як моҳияти муайянро нигоҳ медорад; маълумоти такрорӣ кам карда шудааст.
4. Моҳиятҳо ва атрибутҳо
Нишонаҳо: PK — калиди асосӣ; FK — калиди берунӣ; UQ — UNIQUE; NOT NULL — ҳатмӣ; NULL — ихтиёрӣ.
4.1. users — корбарон
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами корбар
full_name	VARCHAR(150)		не	Ному насаби корбар
email	VARCHAR(150)	UQ	не	Почтаи электронӣ
phone	VARCHAR(20)		ҳа	Рақами телефон
password_hash	VARCHAR(255)		не	Рамзи рамзгузоришуда
role	ENUM('passenger','admin')		не	Нақши корбар
created_at	DATETIME		не	Санаи сабт


4.2. stations — истгоҳҳо
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами истгоҳ
name	VARCHAR(120)		не	Номи истгоҳ
city	VARCHAR(100)		не	Шаҳр
code	VARCHAR(20)	UQ	не	Рамзи ягонаи истгоҳ


4.3. trains — қатораҳо
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами қатора дар ПД
train_number	VARCHAR(20)	UQ	не	Рақами қатора
train_name	VARCHAR(100)		не	Номи қатора
train_type	VARCHAR(50)		не	Навъи қатора
total_cars	INT		не	Шумораи вагонҳо
status	ENUM('active','inactive')		не	Ҳолати қатора


4.4. routes — хатсайрҳо
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами хатсайр
route_name	VARCHAR(150)		не	Номи хатсайр
start_station_id	INT	FK → stations.id	не	Истгоҳи оғоз
end_station_id	INT	FK → stations.id	не	Истгоҳи анҷом


4.5. route_stations — истгоҳҳои хатсайр
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами сабт
route_id	INT	FK → routes.id	не	Хатсайр
station_id	INT	FK → stations.id	не	Истгоҳ
stop_order	INT		не	Тартиби истгоҳ
arrival_offset_minutes	INT		ҳа	Дақиқа аз оғози ҳаракат то расидан
departure_offset_minutes	INT		ҳа	Дақиқа аз оғози ҳаракат то рафтан


4.6. trips — ҳаракатҳои қатора
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами ҳаракат
train_id	INT	FK → trains.id	не	Қатора
route_id	INT	FK → routes.id	не	Хатсайр
departure_datetime	DATETIME		не	Вақти рафтан
arrival_datetime	DATETIME		не	Вақти расидан
base_price	DECIMAL(10,2)		не	Нархи ибтидоӣ
status	ENUM('scheduled','cancelled','completed')		не	Ҳолати ҳаракат


4.7. seats — ҷойҳо
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами ҷой дар ПД
train_id	INT	FK → trains.id	не	Қатора
car_number	INT		не	Рақами вагон
seat_number	VARCHAR(10)		не	Рақами ҷой
seat_type	ENUM('standard','luxury')		не	Навъи ҷой


4.8. bookings — фармоишҳои чипта
Атрибут	Навъ	Калид	NULL	Тавсиф
id	INT	PK	не	Рақами фармоиш
user_id	INT	FK → users.id	ҳа	Корбари фармоишдиҳанда
trip_id	INT	FK → trips.id	не	Ҳаракати интихобшуда
seat_id	INT	FK → seats.id	не	Ҷойи интихобшуда
booking_code	VARCHAR(30)	UQ	не	Рамзи фармоиш
passenger_name	VARCHAR(150)		не	Номи мусофир
passenger_document	VARCHAR(50)		ҳа	Ҳуҷҷати мусофир
price	DECIMAL(10,2)		не	Нархи чипта
status	ENUM('pending','paid','cancelled')		не	Ҳолати фармоиш
booked_at	DATETIME		не	Санаи фармоиш


5. Алоқаҳо
№	Алоқа	Навъ	Калиди берунӣ
1	users — bookings	1:N	bookings.user_id
2	trains — trips	1:N	trips.train_id
3	routes — trips	1:N	trips.route_id
4	trains — seats	1:N	seats.train_id
5	routes — stations	M:N	route_stations.route_id, route_stations.station_id
6	trips — bookings	1:N	bookings.trip_id
7	seats — bookings	1:N	bookings.seat_id
8	stations — routes (оғоз)	1:N	routes.start_station_id
9	stations — routes (анҷом)	1:N	routes.end_station_id


Алоқаи M:N: як хатсайр аз якчанд истгоҳ мегузарад ва як истгоҳ метавонад дар якчанд хатсайр истифода шавад. Ҷадвали route_stations ин алоқаро ба ду алоқаи 1:N табдил медиҳад.
6. Маҳдудиятҳои бутунӣ
UNIQUE
1. users.email — почтаи электронӣ такрор нашавад.
2. trains.train_number — рақами қатора такрор нашавад.
3. stations.code — рамзи истгоҳ такрор нашавад.
Маҳдудиятҳои иловагии тавсияшаванда:
- bookings.booking_code UNIQUE.
- route_stations(route_id, stop_order) UNIQUE.
- seats(train_id, car_number, seat_number) UNIQUE.
- bookings(trip_id, seat_id) UNIQUE, то як ҷой дар як ҳаракат ду бор фармоиш дода нашавад.
CHECK
trips.arrival_datetime > trips.departure_datetime — вақти расидан бояд аз вақти рафтан дертар бошад.

Ҳамчунин:
- trips.base_price >= 0
- trips.departure_datetime < trips.arrival_datetime
- trains.total_cars > 0
ON DELETE барои ҳамаи FK
FK	Қоида	Сабаб
routes.start_station_id	RESTRICT	Истгоҳи оғози истифодашавандаро нест кардан мумкин нест
routes.end_station_id	RESTRICT	Истгоҳи анҷоми истифодашавандаро нест кардан мумкин нест
route_stations.route_id	CASCADE	Ҳангоми нест шудани хатсайр сабтҳои пайвасткунанда низ нест мешаванд
route_stations.station_id	RESTRICT	Истгоҳи истифодашавандаро нест кардан мумкин нест
trips.train_id	RESTRICT	Қаторае, ки ҳаракат дорад, нест карда намешавад
trips.route_id	RESTRICT	Хатсайри истифодашавандаро нест кардан мумкин нест
seats.train_id	CASCADE	Ҳангоми нест шудани қатораи истифоданашаванда ҷойҳои он низ нест мешаванд
bookings.user_id	SET NULL	Фармоиш нигоҳ дошта шуда, пайванди корбар тоза мешавад
bookings.trip_id	RESTRICT	Ҳаракати дорои фармоишро нест кардан мумкин нест
bookings.seat_id	RESTRICT	Ҷойи дорои фармоишро нест кардан мумкин нест


Эзоҳ: CASCADE барои trains → seats танҳо вақте иҷозат дода мешавад, ки қатора фармоишҳои вобаста надошта бошад; bookings.seat_id бо RESTRICT несткунии ҷойи фармоишшударо манъ мекунад.
7. Хулоса
Модели мантиқии пойгоҳи додаҳои системаи фурӯши чиптаҳои қатора таҳия карда шуд. Ҳашт ҷадвал бо атрибутҳо, PK, FK ва қоидаҳои NULL муайян гардиданд. Нормализатсия то 3ШН иҷро шуда, алоқаҳои 1:N ва M:N муайян карда шуданд. Маҳдудиятҳои UNIQUE, CHECK ва ON DELETE барои нигоҳ доштани бутунии додаҳо муқаррар гардиданд.