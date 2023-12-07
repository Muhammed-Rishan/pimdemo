<?php

namespace App\Document\Areabrick;

class Pdf extends AbstractAreabrick
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'PDF';
    }
}
