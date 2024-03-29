<?php

use Holgerk\EqualGolden\Plugin;

Plugin::$updateGolden = true;
expect('hello')->toEqualGolden('different');
