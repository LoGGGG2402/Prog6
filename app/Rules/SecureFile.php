<?php

namespace App\Rules;

use App\Helpers\FileValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SecureFile implements ValidationRule
{
    protected $allowedTypes;
    protected $errorMessage = 'The file must be a valid and secure file of the allowed types.';

    /**
     * Create a new rule instance.
     *
     * @param array $allowedTypes Allowed file type categories
     * @return void
     */
    public function __construct(array $allowedTypes)
    {
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            FileValidator::validate($value, $this->allowedTypes);
        } catch (FileException $e) {
            $this->errorMessage = $e->getMessage();
            $fail($this->errorMessage);
        }
    }
}
