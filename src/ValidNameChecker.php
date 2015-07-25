<?php


namespace Beanie;


use Beanie\Exception\InvalidNameException;

class ValidNameChecker
{
    const VALID_NAME_REGEX = '/^[A-Za-z0-9+\/;.$_()][A-Za-z0-9+\/;.$_()\-]*$/';

    /**
     * @param string $name
     * @return bool
     * @throws InvalidNameException
     */
    public function ensureValidName($name)
    {
        if (!(
            is_string($name) &&
            strlen($name) <= 200 &&
            preg_match(self::VALID_NAME_REGEX, $name)
        )) {
            throw new InvalidNameException(sprintf(
                'Invalid name: \'%s\'', $this->asString($name)
            ));
        }

        return true;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function asString($value)
    {
        return (is_object($value) && !method_exists($value, '__toString'))
            ? sprintf('{object of type %s', get_class($value))
            : $value;
    }
}
