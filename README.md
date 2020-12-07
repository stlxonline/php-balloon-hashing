# php-balloon-hashing
Balloon hashing algorithm implementation written in PHP. All credit to Dan Boneh, Henry Corrigan-Gibbs, and Stuart Schechter. For more information see
the [research paper](https://eprint.iacr.org/2016/027.pdf) or their [website](https://crypto.stanford.edu/balloon/) for this project. Based on [python implementation](https://github.com/nachonavarro/balloon-hashing).

# USAGE:

```php
include('balloon.php');
$balloon_hash = balloon_hash("mypassword", "mysalt");
echo $balloon_hash;
```

# CONFIGURATION:

You can customize the delta, time cost and space cost variables on balloon_hash function:

```php
$delta = 5;
$time_cost = 18;
$space_cost = 24;
```

Also, you can change the main hashing algorithm ([more info](https://www.php.net/manual/es/function.hash.php)) used on hash_func_hex and hash_func functions:

```php
return hash('sha256', $t, TRUE);

return hash('sha256', $t);
```
