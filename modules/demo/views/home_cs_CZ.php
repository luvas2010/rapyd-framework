Ahoj, toto je Rapyd Demo Module

Požadavky na server pro spuštění tohoto dema

- php >= 5.1
- pdo_sqlite - tento ovladač musí být zapnut *
- složky&soubory /modules/demo/db/ musí mít nastaveno povolení rwx (777)

Toto demo používá soubor sqlite3 databáze, ale rapyd má také ovladače pro mysql a postgres.

* na debian/ubuntu můžete nainstalovat sqlite ovladač s:
sudo apt-get install php5-sqlite

* v prostředí windows zkontrolujte php.ini a odkomentujte:
extension=php_pdo_sqlite.dll