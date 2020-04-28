<?php

namespace Shared\Domain\Model\Form;

use Resources\Exception\RegularException;

class FieldVO
{

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var string
     */
    protected $position = null;

    /**
     *
     * @var bool
     */
    protected $mandatory;

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    function getPosition(): ?string
    {
        return $this->position;
    }

    function isMandatory(): bool
    {
        return $this->mandatory;
    }

    protected function __construct()
    {
        ;
    }

    public function assertMandatoryRequirementSatisfied($value): void
    {
        if ($this->mandatory && empty($value)) {
            $errorDetail = "bad request: {$this->name} field is required";
            throw RegularException::badRequest($errorDetail);
        }
    }

}
