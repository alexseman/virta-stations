<?php

declare(strict_types=1);

namespace App\Dto\Company;

class UpdateCompanyDto
{
    public function __construct(
        public string $name,
        public ?int   $parentId
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
