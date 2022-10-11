<?php

class PER80
{
    readonly public \DateTime $keywords;

    public function firstClassCallables()
    {
        foo( ... );
    }
}

enum  Suit :string{
    case Hearts = 'H';case Diamonds = 'D';
    case Spades = 'S';case Clubs = 'C';

    protected function foo(): void
    {
    }
}
