<?php

namespace App\Twig;

use Cocur\Slugify\Slugify;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('from_camel_case', [$this, 'fromCamelCase']),
            new TwigFilter('slug', [$this, 'slugFilter']),
            new TwigFilter('uk_postcode', [$this, 'ukPostcodeFilter']),
        ];
    }

    public function getTests()
    {
        return [
            new \Twig\TwigTest('instanceof', [$this, 'instanceOf']),
        ];
    }

    /**
     * Converts camelCase string to have spaces between each.
     *
     * @param $camelCaseString
     */
    public function fromCamelCase(string $camelCaseString): string
    {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);

        return ucfirst(join($a, ' '));
    }

    public function slugFilter(string $str): string
    {
        $slugify = new Slugify();

        return $slugify->slugify($str);
    }

    public function instanceOf($value, $className)
    {
        return get_class($value) === $className;
    }

    public function ukPostcodeFilter(string $postcode): string
    {
        $postcode = str_replace(' ', '', $postcode);
        $pos = strlen($postcode) - 3;
        $str = substr_replace($postcode, ' ', $pos, 0);

        return $str;
    }
}
