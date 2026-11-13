# Дастури ба кор андохтан ва ҷойгиркунӣ: railway-tickets
# Инструкция по развёртыванию веб-проекта

## 1. Талаботи системавӣ
- **ОС:** Windows 10/11, Ubuntu 22.04 LTS ё дигар системаи Unix.
- **Веб-сервер:** Apache 2.4+ бо фаъоли `mod_rewrite` ё Nginx 1.20+.
- **PHP:** Версияи 8.2 ё болотар (васеъкуниҳои `pdo`, `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`).
- **МБДД:** MariaDB 10.4+ / MySQL 8.0+ (ё файли маҳаллии SQLite).

---

## 2. Тарзи ба кор андохтан бо сервери дарунсохти PHP (Усули зуд)

1. Дар сатри фармон ба ҷузвдони решавии лоиҳа гузаред:
   ```bash
   cd railway-tickets
   ```
2. Сервери санҷиширо ба кор дароред:
   ```bash
   php -S localhost:8000 -t public
   ```
3. Дар браузер саҳифаро кушоед:
   ```text
   http://localhost:8000
   ```
   *(Эзоҳ: Пойгоҳи додаҳо ҳангоми аввалин бор кушодан дар ҳолати худкор бо маълумоти пурраи намунавӣ ва ҷойҳо омода карда мешавад).*

---

## 3. Тарзи ба кор андохтан дар муҳити XAMPP (Windows)

1. Ҷузвдони `railway-tickets`-ро ба суроғаи зерин гузоред:
   ```text
   C:\xampp\htdocs\railway-tickets
   ```
2. Панели XAMPP Control Panel-ро кушода, **Apache** ва **MySQL**-ро оғоз намоед (`Start`).
3. Дар phpMyAdmin ворид шуда (`http://localhost/phpmyadmin`):
   - Пойгоҳи додаҳои навро бо номи `railway_tickets` созед (кодировка: `utf8mb4_unicode_ci`).
   - Файли `database/schema.sql`-ро импорт намоед.
   - Файли `database/seed.sql`-ро импорт намоед.
4. Файли `config/config.php`-ро санҷед:
   ```php
   'db' => [
       'driver'   => 'mysql',
       'host'     => '127.0.0.1',
       'port'     => 3306,
       'database' => 'railway_tickets',
       'username' => 'root',
       'password' => '',
       'charset'  => 'utf8mb4'
   ]
   ```
5. Дар браузер ворид шавед:
   ```text
   http://localhost/railway-tickets/public/
   ```

---

## 4. Ҳисобҳои корбарии омода барои санҷиш

- **Маъмури низом (Администратор):**
  - Логин: `admin`
  - Рамз: `admin123`
- **Кассири кассаи вокзал:**
  - Логин: `cashier1`
  - Рамз: `cashier123`
- **Мусофири сабтшуда:**
  - Логин: `passenger1`
  - Рамз: `pass123`
