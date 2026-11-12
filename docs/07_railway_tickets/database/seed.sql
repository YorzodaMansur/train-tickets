USE railway_tickets;
SET NAMES utf8mb4;
-- Пароли барои санҷиш: password. Hash-и bcrypt; дар лоиҳаи воқеӣ hash-и инфиродӣ истифода шавад.
SET @h = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi';

INSERT INTO users(id,username,password_hash,full_name,email,phone,role,is_active) VALUES
(1,'admin',@h,'Мансур Ёров','admin@example.com','+992900000001','admin',1),
(2,'dispatcher1',@h,'Фаридун Саидов','dispatch@example.com','+992900000002','dispatcher',1),
(3,'passenger1',@h,'Фарҳод Алиев','farhod@example.com','+992900000003','passenger',1),
(4,'passenger2',@h,'Мадина Каримова','madina@example.com','+992900000004','passenger',1),
(5,'passenger3',@h,'Ҷамшед Раҳимов','jamshed@example.com','+992900000005','passenger',1);

INSERT INTO stations VALUES
(1,'DUS','Душанбе','Душанбе','Душанбе'),
(2,'VHD','Ваҳдат','Вахдат','Ваҳдат'),
(3,'AIN','Айнӣ','Айни','Айнӣ'),
(4,'KHU','Хуҷанд','Худжанд','Хуҷанд'),
(5,'IST','Истаравшан','Истаравшан','Истаравшан'),
(6,'TUR','Турсунзода','Турсунзаде','Турсунзода');

INSERT INTO trains VALUES
(1,'TR-101','Сомон','Шаҳрӣ','active'),
(2,'TR-102','Истаравшан','Байнишаҳрӣ','active'),
(3,'TR-103','Ваҳдат','Байнишаҳрӣ','active');

INSERT INTO routes VALUES
(1,'DUS-KHU','Душанбе — Хуҷанд',1,4,300,360),
(2,'DUS-IST','Душанбе — Истаравшан',1,5,250,300),
(3,'DUS-TUR','Душанбе — Турсунзода',1,6,60,90);

INSERT INTO route_stations VALUES
(1,1,1,1,NULL,0),(2,1,3,2,150,155),(3,1,4,3,360,NULL),
(4,2,1,1,NULL,0),(5,2,2,2,60,65),(6,2,5,3,300,NULL),
(7,3,1,1,NULL,0),(8,3,2,2,45,50),(9,3,6,3,90,NULL);

INSERT INTO trips VALUES
(1,1,1,'2026-12-01 07:00:00','2026-12-01 13:00:00','scheduled',250),
(2,1,1,'2026-12-02 07:00:00','2026-12-02 13:00:00','scheduled',250),
(3,1,1,'2026-12-03 07:00:00','2026-12-03 13:00:00','scheduled',255),
(4,1,1,'2026-12-04 07:00:00','2026-12-04 13:00:00','boarding',250),
(5,2,2,'2026-12-01 08:00:00','2026-12-01 13:00:00','scheduled',180),
(6,2,2,'2026-12-02 08:00:00','2026-12-02 13:00:00','scheduled',180),
(7,2,2,'2026-12-03 08:00:00','2026-12-03 13:00:00','completed',185),
(8,2,2,'2026-12-04 08:00:00','2026-12-04 13:00:00','scheduled',180),
(9,3,3,'2026-12-01 09:00:00','2026-12-01 10:30:00','scheduled',60),
(10,3,3,'2026-12-02 09:00:00','2026-12-02 10:30:00','scheduled',60),
(11,3,3,'2026-12-03 09:00:00','2026-12-03 10:30:00','cancelled',60),
(12,3,3,'2026-12-04 09:00:00','2026-12-04 10:30:00','scheduled',65);

INSERT INTO cars VALUES
(1,1,1,'coupe',3,1.00),(2,1,2,'standard',3,0.90),(3,1,3,'luxury',3,1.50),
(4,2,1,'coupe',3,1.00),(5,2,2,'standard',3,0.90),(6,2,3,'luxury',3,1.50),
(7,3,1,'sitting',3,1.00),(8,3,2,'sitting',3,1.00),(9,3,3,'standard',3,1.10);

INSERT INTO seats VALUES
(1,1,1,'lower','available'),(2,1,2,'upper','available'),(3,1,3,'lower','available'),
(4,2,1,'standard','available'),(5,2,2,'window','available'),(6,2,3,'aisle','available'),
(7,3,1,'lower','available'),(8,3,2,'upper','available'),(9,3,3,'lower','available'),
(10,4,1,'lower','available'),(11,4,2,'upper','available'),(12,4,3,'lower','available'),
(13,5,1,'standard','available'),(14,5,2,'window','available'),(15,5,3,'aisle','available'),
(16,6,1,'lower','available'),(17,6,2,'upper','available'),(18,6,3,'lower','available'),
(19,7,1,'standard','available'),(20,7,2,'window','available'),(21,7,3,'aisle','available'),
(22,8,1,'standard','available'),(23,8,2,'window','available'),(24,8,3,'aisle','available'),
(25,9,1,'standard','available'),(26,9,2,'window','available'),(27,9,3,'aisle','available');

INSERT INTO tickets
(id,ticket_number,trip_id,seat_id,user_id,passenger_name,passenger_doc,price,status,purchase_channel,paid_at,created_at,refunded_at,refunded_by_user_id)
VALUES
(1,'TK-2026-0001',1,1,3,'Фарҳод Алиев','A1234567',250,'paid','website','2026-11-20 10:00:00','2026-11-20 09:55:00',NULL,NULL),
(2,'TK-2026-0002',2,2,4,'Мадина Каримова','B2234567',250,'paid','mobile','2026-11-21 11:00:00','2026-11-21 10:55:00',NULL,NULL),
(3,'TK-2026-0003',3,3,5,'Ҷамшед Раҳимов','C3234567',255,'reserved','website',NULL,'2026-11-22 12:00:00',NULL,NULL),
(4,'TK-2026-0004',4,4,3,'Фарҳод Алиев','A1234567',225,'paid','ticket_office','2026-11-23 08:00:00','2026-11-23 07:50:00',NULL,NULL),
(5,'TK-2026-0005',5,10,4,'Мадина Каримова','B2234567',180,'paid','website','2026-11-20 13:00:00','2026-11-20 12:55:00',NULL,NULL),
(6,'TK-2026-0006',6,11,5,'Ҷамшед Раҳимов','C3234567',180,'cancelled','mobile',NULL,'2026-11-21 13:00:00',NULL,NULL),
(7,'TK-2026-0007',7,12,3,'Фарҳод Алиев','A1234567',185,'paid','website','2026-11-22 13:00:00','2026-11-22 12:55:00',NULL,NULL),
(8,'TK-2026-0008',8,13,4,'Мадина Каримова','B2234567',162,'paid','website','2026-11-23 13:00:00','2026-11-23 12:55:00',NULL,NULL),
(9,'TK-2026-0009',9,19,5,'Ҷамшед Раҳимов','C3234567',60,'paid','ticket_office','2026-11-20 14:00:00','2026-11-20 13:55:00',NULL,NULL),
(10,'TK-2026-0010',10,20,3,'Фарҳод Алиев','A1234567',60,'reserved','website',NULL,'2026-11-21 14:00:00',NULL,NULL),
(11,'TK-2026-0011',11,21,4,'Мадина Каримова','B2234567',60,'refunded','mobile','2026-11-22 14:00:00','2026-11-22 13:55:00','2026-11-22 15:00:00',2),
(12,'TK-2026-0012',12,22,5,'Ҷамшед Раҳимов','C3234567',65,'paid','website','2026-11-23 14:00:00','2026-11-23 13:55:00',NULL,NULL);
