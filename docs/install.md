Install
=======

* Edit app.ini file and place it under application/config/
* create tables using kode.sql
* create an apache alias or vhost and point it to kode/public/
* create another apache alias or vhost and point it to kode/public/admin
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



