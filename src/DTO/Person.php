<?php

namespace StreetCsv\DTO;

use ArrayAccess;

/**
 * @implements ArrayAccess<string, mixed>
 */
class Person implements ArrayAccess
{
    public ?string $title;
    public ?string $first_name;
    public ?string $initial;
    public ?string $last_name;

    public function __construct(?string $title, ?string $firstName, ?string $initial, ?string $lastName)
    {
        $this->title = $title;
        $this->first_name = $firstName;
        $this->initial = $initial;
        $this->last_name = $lastName;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->$offset = null;
    }

    public function __toString() {
        return $this->title.' '. ($this->first_name ?? $this->initial) . ' ' . $this->last_name;
    }
}