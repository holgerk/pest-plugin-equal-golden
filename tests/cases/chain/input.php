<?php

expect('hello')
    ->toEqualGolden(null)
    ->toEqual('hello')
    ->toEqualGolden(null);
