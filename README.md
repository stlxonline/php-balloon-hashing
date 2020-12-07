# php-balloon-hashing
Balloon hashing algorithm implementation written in PHP

# USAGE:

include('balloon.php');

$balloon_hash = balloon_hash("mypassword", "mysalt");

echo $balloon_hash;

# CONFIGURATION:

You can customize the delta, time cost and space cost variables on balloon_hash function:

$delta = 5;

$time_cost = 18;

$space_cost = 24;
