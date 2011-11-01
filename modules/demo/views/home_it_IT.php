Salve, questo è il modulo Demo di Rapyd

Requisiti del server per questa demo:

- php> = 5,1
- driver pdo_sqlite abilitato *
- la cartella e i file contenuti in /modules/demo/db/ devono essere scrivibili: rwx (777)

Questa demo utilizza un database su flat-file (sqlite3), ma rapyd ha anche i driver per mysql e postgres.


* Su una Debian / Ubuntu è possibile installare sqlite con:
sudo apt-get install php5-sqlite

* Su windows basta decommentare una riga nel php.ini:
extension = php_pdo_sqlite.dll
