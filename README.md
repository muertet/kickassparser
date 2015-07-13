[![Analytics](https://ga-beacon.appspot.com/UA-17476024-7/kickassparser/readme?pixel)](https://github.com/muertet/kickassparser)

KickAss backup parser
=========

This class allows you to download and parse, daily and hourly KickAss torrent db backups.

**UPDATE** KickAssTorrents have changed their T.O.S. Now you need to fill a request to access their backups, so this class has been deprecated.

Demo
----

```php
$kickass = new KickAss();
$kickass->getDump('hourly'); //downloads the latest hourly dump

$kickass->parse('hourly', function ($data) { //callback function for each parsed torrent
    echo $data['name']."<br>";
});
```
