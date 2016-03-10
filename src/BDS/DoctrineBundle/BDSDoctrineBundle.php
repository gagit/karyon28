<?php

namespace BDS\DoctrineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BDSDoctrineBundle extends Bundle
{
    public function getParent() {
        return 'DoctrineBundle';
    }
    
}
