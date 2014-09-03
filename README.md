![Analytics](https://ga-beacon.appspot.com/UA-17476024-7/kickassparser/readme?pixel)](https://github.com/muertet/kickassparser)

KickAss backup parser
=========

This class allows you to download and parse, daily and hourly KickAss torrent db backups.

Demo
----

```php
$kickass = new KickAss();
$kickass->getDump('hourly'); //downloads the latest hourly dump

$kickass->parse('hourly', function ($data) { //callback function for each parsed torrent
    echo $data['name']."<br>";
});
```
