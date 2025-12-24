<?php

namespace App\ReqFilter\CriteriaDto\Common;
use phpDocumentor\Reflection\Types\Boolean;

final class UnionPart
{

    /** @var UnionCriteria[] $parts  */
    private array $parts;
    private function __construct() {}
    public static function create(): self{return new self();}
    public function setPart(UnionCriteria $part): self
    {
        $this->parts[] = $part;
        return $this;
    }

    public function getParts(): array
    {
        return $this->parts;
    }
}
