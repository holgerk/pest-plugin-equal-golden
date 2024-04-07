<?php

use Holgerk\EqualGolden\Plugin;

Plugin::$forceUpdateGolden = true;
expect('hello')->toEqualGolden('different');
