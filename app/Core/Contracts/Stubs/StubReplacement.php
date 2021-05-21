<?php

namespace App\Core\Contracts\Stubs;

use App\Core\Helpers\IO\IO;

abstract class StubReplacement
{

    /**
     * The text to show to the user when prompting for the replacement
     *
     * @var string
     */
    private string $questionText;

    /**
     * The name of the variable this replacement holds
     *
     * @var string
     */
    private string $variableName;

    /**
     * The default value for the replacement. Defaults to null.
     *
     * @var null
     */
    private $default = null;

    /**
     * The validation to run on the value
     *
     * The function should accept a single argument, a value, and return a boolean indicating if the value is valid.
     *
     * @var \Closure|null
     */
    private ?\Closure $validator = null;

    /**
     * Get the name of the variable this replaces
     *
     * @return string
     */
    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * Set the name of the variable this replaces
     *
     * @param string $variableName
     * @return StubReplacement
     */
    public function setVariableName(string $variableName): StubReplacement
    {
        $this->variableName = $variableName;
        return $this;
    }

    public function appendData(array $data = [], bool $useDefault = false): array
    {
        $data[$this->getVariableName()] = ($useDefault && $this->hasDefault() ? $this->getDefault() : $this->getValue());
        return $data;
    }

    /**
     * Get the default value
     *
     * @param mixed $default The default to use if a default is not set.
     * @return null|mixed
     */
    protected function getDefault($default = null)
    {
        return $this->hasDefault() ? $this->default : $default;
    }

    /**
     * Checks if a default value exists.
     *
     * @return bool
     */
    public function hasDefault(): bool
    {
        return isset($this->default) && $this->default !== null;
    }

    /**
     * @return \Closure|null
     */
    public function getValidator(): ?\Closure
    {
        return $this->validator;
    }

    /**
     * @param \Closure|null $validator
     * @return StubReplacement
     */
    public function setValidator(?\Closure $validator): StubReplacement
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @param null $default
     * @return StubReplacement
     */
    public function setDefault($default): StubReplacement
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuestionText(): string
    {
        return $this->questionText;
    }

    /**
     * @param string $questionText
     * @return StubReplacement
     */
    public function setQuestionText(string $questionText): StubReplacement
    {
        $this->questionText = $questionText;
        return $this;
    }

    /**
     * Ask the question to the user to get the value
     *
     * @return mixed
     */
    abstract protected function askQuestion();

    /**
     * Validate the value is usable by the replacement type
     *
     * @param mixed $value
     * @return bool
     */
    abstract protected function validateType($value): bool;

    /**
     * Validate if the given value can be used
     *
     * @param $value
     * @return bool
     */
    protected function validate($value): bool
    {
        if($this->getValidator() === null) {
            return true;
        }
        if($this->validateType($value) === false) {
            return false;
        }
        return $this->getValidator()($value);
    }

    /**
     * Get the value of the replacement from the user
     *
     * @return mixed
     */
    protected function ask()
    {
        $value = $this->askQuestion();

        if(!$this->validate($value)) {
            IO::error(sprintf('[%s] is not a valid value.', $value));
            return $this->getValue();
        }

        return $value;
    }

    protected function getValue()
    {
        return $this->ask();
    }

    public static function new(string $variableName, string $questionText, $default = null, ?\Closure $validator = null): StubReplacement
    {
        $replacement = new static();
        $replacement->setVariableName($variableName);
        $replacement->setQuestionText($questionText);
        $replacement->setDefault($default);
        $replacement->setValidator($validator);
        return $replacement;
    }

    /**
     * Parse the string input from the command line into the right type
     * @param string $variable
     * @return mixed
     */
    public function parseCommandInput(string $variable)
    {
        return $variable;
    }

}
