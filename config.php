<?php
// Enable tolerance option by default.
define('TOLERANCE', true);

// Set tolerance in minutes. Overwrites preset in class.
define('MINTOLERANCE', 2);
define('MAXTOLERANCE', 2);

// Secret key to encrypt credentials.
define('KEY', 'secret');

// Lifetime of generated token.
define('LIFETIME', 'now -2 months');
