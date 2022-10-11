<?php

class PER80
{
    public readonly \DateTime $keywords;

    public function firstClassCallables()
    {
        foo(...);
    }
}

enum Suit: string
{
    case Hearts = 'H';
    case Diamonds = 'D';
    case Spades = 'S';
    case Clubs = 'C';

    private function foo(): void
    {
    }
}
