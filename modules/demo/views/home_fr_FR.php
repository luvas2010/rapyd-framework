Salut, ceci est le module de démonstration

Configuration du serveur pour cette démo:

- php >= 5.1
- driver pdo_sqlite activé *
- Le répertoire /modules/demo/db/ doit avoir l'autorisation rwx (777)

Cette démo utiliser une database sqlite3, mais rapyd a également des pilotes pour mySQL et postgres.


* sur une Debian / Ubuntu, vous pouvez installer le driver sqlite avec:
sudo apt-get install php5-sqlite

* Sous Windows, vous devez décommenter une ligne dans le fichier php.ini:
extension = php_pdo_sqlite.dll