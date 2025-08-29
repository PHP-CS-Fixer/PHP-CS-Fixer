<?php

namespace Vendor\Package;

class Foo
{
    public function testStringImplicitBackslashes($a = 'a\b'): string
    {
        return 'a\b'
            .$a
            ."\n\\a\\b";
    }
}
