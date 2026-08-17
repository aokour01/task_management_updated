<?php

namespace App\Core;

/**
 * A small fluent validator. Each rule appends a human-readable message to
 * an internal error list; call errors() at the end to get them all.
 */
class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function required(string $field, string $label): static
    {
        if (trim((string) ($this->data[$field] ?? '')) === '') {
            $this->errors[] = "{$label} is required.";
        }

        return $this;
    }

    public function email(string $field, string $label): static
    {
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = "{$label} must be a valid email address.";
        }

        return $this;
    }

    public function minLength(string $field, int $length, string $label): static
    {
        $value = (string) ($this->data[$field] ?? '');

        if (mb_strlen($value) < $length) {
            $this->errors[] = "{$label} must be at least {$length} characters.";
        }

        return $this;
    }

    public function maxLength(string $field, int $length, string $label): static
    {
        $value = (string) ($this->data[$field] ?? '');

        if (mb_strlen($value) > $length) {
            $this->errors[] = "{$label} must be no more than {$length} characters.";
        }

        return $this;
    }

    public function matches(string $field, string $otherField, string $label): static
    {
        $value = $this->data[$field] ?? '';
        $other = $this->data[$otherField] ?? '';

        if ($value !== $other) {
            $this->errors[] = "{$label} does not match.";
        }

        return $this;
    }

    public function in(string $field, array $allowed, string $label): static
    {
        $value = $this->data[$field] ?? null;

        if (!in_array($value, $allowed, true)) {
            $this->errors[] = "{$label} is invalid.";
        }

        return $this;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
