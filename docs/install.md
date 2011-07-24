Install
=======

* Create tables using kode.sql
    * create a database in mysql. Name it what ever you like.
    * import `docs/sql/kode.sql` into your newly created database.
* Edit `docs/app.ini` file and place it under application/config/
    * set `resources.db.params.dbname` to the name you just created. default is `kode`
    * set mysql username: `resources.db.params.username = "USERNAMEHERE"`
    * set mysql password: `resources.db.params.password = "PASSWORDHERE"`
    * config everything else there if you like
* Rename environment.example.php to environment.php and configure it base on your needs.
* create an apache alias or vhost and point it to kode/public/
* create these folders under kode/data and set necessary permissions:
    cache/
    logs/admin/missing_translations
    logs/frontend/missing_translations
    sessions/
    uploads/
* Download lates Zend framework from [here](http://framework.zend.com/download/current/)
  and extract it under library so you will have library/Zend/*
* Now you are ready!
* Username: admin, Password:lorem



