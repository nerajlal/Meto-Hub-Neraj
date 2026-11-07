<?php

namespace App\Exceptions;

// A provider explicitly rejected setup before a usable payment checkout was opened.
class PaymentSetupRejected extends \RuntimeException {}
