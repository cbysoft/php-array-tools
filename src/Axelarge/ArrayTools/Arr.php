<?php
namespace Axelarge\ArrayTools;

/**
 * @license MIT License
 * @license www.opensource.org/licenses/MIT
 */
class Arr implements \ArrayAccess, \Iterator
{
    /** @var array */
    protected $arr;

    public function __construct(array $arr = array())
    {
        $this->arr = $arr;
    }

    public static function wrap(array $arr)
    {
        return new static($arr);
    }

    /**
     * Short-hand for wrap()
     *
     * <code>
     * use Arr as A;
     * A::w([1, 2, 3])
     * </code>
     *
     * @see wrap()
     * @param array $arr
     * @return static
     */
    public static function w(array $arr)
    {
        return new static($arr);
    }

    /**
     * Create a new instance from function arguments
     *
     * @return static
     */
    public static function create()
    {
        return new static(func_get_args());
    }

    /**
     * Returns the underlying array
     *
     * @return array
     */
    public function raw()
    {
        return $this->arr;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return print_r($this->arr, true);
    }

    /**
     * Returns a clone of the object
     *
     * @return static
     */
    public function dup()
    {
        $dup = clone $this;
        return $dup;
    }

    /**
     * Reverses the array
     *
     * @param bool $preserveKeys
     * @return static
     */
    public function reverse($preserveKeys = false)
    {
        return new static(array_reverse($this->arr, $preserveKeys));
    }

    public function put($key, $value)
    {
        $this->arr[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return $this->arr[$key];
    }

    public function getOrElse($key, $default = null)
    {
        return isset($this->arr[$key]) ? $this->arr[$key] : $default;
    }

    public static function _getOrPut(&$array, $key, $default = null)
    {
        if (!isset($array[$key])) {
            $array[$key] = $default;
        }

        return $array[$key];
    }

    public function getOrPut($key, $default = null)
    {
        if (!isset($this->arr[$key])) {
            $this->arr[$key] = $default;
        }

        return $this->arr[$key];
    }

    public static function _getAndDelete(&$array, $key, $default = null)
    {
        if (isset($array[$key])) {
            $result = $array[$key];
            unset($array[$key]);
            return $result;
        } else {
            return $default;
        }
    }

    public function getAndDelete($key, $default = null)
    {
        if (isset($this->arr[$key])) {
            $result = $this->arr[$key];
            unset($this->arr[$key]);
            return $result;
        } else {
            return $default;
        }
    }

    /**
     * Returns the first value
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->arr);
    }

    public static function _find($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Returns the first value satisfying the predicate or null
     *
     * @param \callable $predicate
     * @return mixed|null
     */
    public function find($predicate)
    {
        return static::_find($this->arr, $predicate);
    }

    public static function _findKey($array, $predicate)
    {
        foreach ($array as $key => $value) {
            if ($predicate($value, $key)){
                return $key;
            }
        }

        return null;
    }

    public function findKey($predicate)
    {
        return static::_findKey($this->arr, $predicate);
    }

    public function indexOf($value, $strict = true)
    {
        return array_search($value, $this->arr, $strict);
    }

    /**
     * Returns the last value
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->arr);
    }

    /**
     * Checks if the key exists in the array
     *
     * @param int|string $key
     * @return bool
     */
    public function hasKey($key)
    {
        return array_key_exists($key, $this->arr);
    }

    /**
     * Checks if the value exists in the array
     *
     * @param mixed $value
     * @param bool $strict Whether to use strict comparison for determining equality
     * @return bool
     */
    public function hasValue($value, $strict = true)
    {
        return in_array($value, $this->arr, $strict);
    }

    /**
     * Returns the length of the array
     *
     * @return int
     */
    public function length()
    {
        return count($this->arr);
    }

    /**
     * Checks if the array is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->arr);
    }

    /**
     * Returns the keys of the array
     *
     * @return static
     */
    public function keys()
    {
        return new static(array_keys($this->arr));
    }

    /**
     * Returns the values of the array
     *
     * @return static
     */
    public function values()
    {
        return new static(array_values($this->arr));
    }

    /**
     * Returns a slice of the array
     *
     * @param int $offset
     * @param int $length
     * @param bool $preserveKeys
     * @return static
     */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return new static(array_slice($this->arr, $offset, $length, $preserveKeys));
    }

    /**
     * @param int $offset
     * @param int $length
     * @param array|Arr $replacement
     * @return static
     */
    public function splice($offset, $length = null, $replacement = null)
    {
        $replacement instanceof Arr and $replacement = $replacement->raw();
        $new = $this->arr;
        array_splice($new, $offset, $length, $replacement);
        return new static($new);
    }

    /**
     * Returns the first $n elements or the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function take($n)
    {
        $new = $n >= 0
            ? array_slice($this->arr, 0, $n)
            : array_slice($this->arr, $n);
        return new static($new);
    }

    /**
     * Returns all but the first $n elements or all but the last -$n elements if $n is negative
     *
     * @param int $n
     * @return static
     */
    public function drop($n)
    {
        $new = $n >= 0
            ? array_slice($this->arr, $n)
            : array_slice($this->arr, 0, $n);

        return new static($new);
    }

    public function unique()
    {
        return new static(array_unique($this->arr));
    }

    /**
     * Joins the array values into a string, separated by $separator
     *
     * @param string $separator
     * @return string
     */
    public function join($separator = '')
    {
        return implode($separator, $this->arr);
    }

    public function repeat($n)
    {
        $result = array();
        while ($n-- > 0) {
            foreach ($this->arr as $value) {
                $result[] = $value;
            }
        }

        return new static($result);
    }

    /**
     * Appends an element to the end of the array
     *
     * @param mixed $value
     * @return static
     */
    public function push($value)
    {
        array_push($this->arr, $value);
        return $this;
    }

    /**
     * Removes the last element of the array and returns it
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->arr);
    }

    /**
     * Prepends an element to the front of the array
     *
     * @param mixed $value
     * @return static
     */
    public function unshift($value)
    {
        array_unshift($this->arr, $value);
        return $this;
    }

    /**
     * Removes the first element of the array and returns it
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->arr);
    }

    public static function _only($arr, $keys)
    {
        return array_intersect_key($arr, array_flip($keys));
    }

    /**
     * Returns only those values whose keys are present in $keys
     *
     * Scalar values can also be passed as multiple arguments
     * <code>
     * Arr::create(range('a', 'e'))->only(3, 4); //=> ['d', 'e']
     * </code>
     *
     * @param array|Arr|mixed $keys
     * @return static
     */
    public function only($keys)
    {
        if ($keys instanceof Arr) {
            $keys = $keys->raw();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(array_intersect_key($this->arr, array_flip($keys)));
    }

    /**
     * Returns only those values whose keys are not present in $keys
     *
     * Scalar values can also be passed as multiple arguments
     * <code>
     * Arr::create(range('a', 'e'))->except(2, 4); //=> ['a', 'b', 'd']
     * </code>
     *
     * @param array $arr
     * @param array $keys
     * @return array
     */
    public static function _except($arr, $keys)
    {
        return array_diff_key($arr, array_flip($keys));
    }

    /**
     * Returns only those values whose keys are not present in $keys
     *
     * Scalar values can also be passed as multiple arguments
     * <code>
     * Arr::create(range('a', 'e'))->except(2, 4); //=> ['a', 'b', 'd']
     * </code>
     *
     * @param array|Arr|mixed $keys
     * @return static
     */
    public function except($keys)
    {
        if ($keys instanceof Arr) {
            $keys = $keys->raw();
        } else if (func_num_args() > 1 || !is_array($keys)) {
            $keys = func_get_args();
        }

        return new static(array_diff_key($this->arr, array_flip($keys)));
    }

    /**
     * Returns those values that are present in both arrays
     *
     * <code>
     * Arr::create(1, 2, 3)->intersection([2, 3, 4]) //=> [2, 3]
     * </code>
     *
     * @param array|Arr $other
     * @return static
     */
    public function intersection($other)
    {
        $other instanceof Arr and $other = $other->raw();
        return new static(array_intersect($this->arr, $other));
    }

    /**
     * Returns those values that are not present in the other array
     *
     * <code>
     * Arr::create(1, 2, 3)->difference([2, 3, 4]) //=> [1]
     * </code>
     *
     * @param array|Arr $other
     * @return static
     */
    public function difference($other)
    {
        $other instanceof Arr and $other = $other->raw();
        return new static(array_diff($this->arr, $other));
    }

    /**
     * Returns true if all elements satisfy the given predicate
     *
     * @param \callable $predicate
     * @return bool
     */
    public function all($predicate)
    {
        foreach ($this->arr as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if at least one element satisfies the given predicate
     *
     * @param \callable $predicate
     * @return bool
     */
    public function any($predicate)
    {
        foreach ($this->arr as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if exactly one element satisfies the given predicate
     *
     * @param \callable $predicate
     * @return bool
     */
    public function one($predicate)
    {
        $foundOne = false;
        foreach ($this->arr as $key => $value) {
            if ($predicate($value, $key)) {
                if ($foundOne) {
                    return false;
                } else {
                    $foundOne = true;
                }
            }
        }

        return $foundOne;
    }

    /**
     * Re-indexes the array by either results of the callback or a sub-key
     *
     * @param array $array
     * @param callback|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return array
     */
    public static function _indexBy($array, $callbackOrKey, $arrayAccess = true)
    {
        $indexed = array();

        if (is_string($callbackOrKey)) {
            if ($arrayAccess) {
                foreach ($array as $element) {
                    $indexed[$element[$callbackOrKey]] = $element;
                }
            } else {
                foreach ($array as $element) {
                    $indexed[$element->{$callbackOrKey}] = $element;
                }
            }
        } else {
            foreach ($array as $element) {
                $indexed[$callbackOrKey($element)] = $element;
            }
        }

        return $indexed;
    }

    /**
     * Re-indexes the array by either results of a callback or a sub-key
     *
     * @param callback|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return static $this
     */
    public function indexBy($callbackOrKey, $arrayAccess = true)
    {
        return new static(static::_indexBy($this->arr, $callbackOrKey, $arrayAccess));
    }

    /**
     * @param array $array
     * @param callback|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return array
     */
    public static function _groupBy($array, $callbackOrKey, $arrayAccess = true)
    {
        $groups = array();

        if (is_string($callbackOrKey)) {
            if ($arrayAccess) {
                foreach ($array as $element) {
                    $groups[$element[$callbackOrKey]][] = $element;
                }
            } else {
                foreach ($array as $element) {
                    $groups[$element->{$callbackOrKey}][] = $element;
                }
            }
        } else {
            foreach ($array as $element) {
                $groups[$callbackOrKey($element)][] = $element;
            }
        }

        return $groups;
    }

    /**
     * @param callback|string $callbackOrKey
     * @param bool $arrayAccess Whether to use array or object access when given a key name
     * @return static
     */
    public function groupBy($callbackOrKey, $arrayAccess = true)
    {
        $groups = static::_groupBy($this->arr, $callbackOrKey, $arrayAccess);
        foreach ($groups as &$group) {
            $group = static::wrap($group);
        }

        return new static($groups);
    }


    // split_by

    public static function _sample($array, $size = 1)
    {
        return $size === 1
            ? $array[array_rand($array)]
            : static::_only($array, array_rand($array, $size));
    }

    public function sample($size = 1)
    {
        return $size === 1
            ? $this->arr[array_rand($this->arr)]
            : $this->only(array_rand($this->arr, $size));
    }

    /**
     * @param array|Arr $arr
     * @return static
     */
    public function merge($arr)
    {
        $arr instanceof Arr and $arr = $arr->raw();
        return new static(array_merge($this->arr, $arr));
    }

    /**
     * @param array|static $other
     * @return static
     */
    public function reverseMerge($other)
    {
        $other instanceof static and $other = $other->raw();
        return new static(array_merge($other, $this->arr));
    }

    /**
     * @param array|Arr $arr
     * @return static
     */
    public function combine($arr)
    {
        $arr instanceof Arr and $arr = $arr->raw();
        return new static(array_combine($this->arr, $arr));
    }

    public static function _zip($array1, $array2)
    {
        $args = func_get_args();
        array_unshift($args, null);
        return call_user_func_array('array_map', $args);
    }

    /**
     * @param array|Arr $arr
     * @return static
     */
    public function zip($arr)
    {
        $arr instanceof Arr and $arr = $arr->raw();
        return new static(array_map(null, $this->arr, $arr));
    }

    public function flip()
    {
        return new static(array_flip($this->arr));
    }

    /**
     * Shuffles the array in-place
     *
     * @return Arr
     */
    public function shuffle()
    {
        shuffle($this->arr);
        return $this;
    }

    /**
     * Returns a shuffled copy of the array
     *
     * @return Arr
     */
    public function shuffled()
    {
        return $this->dup()->shuffle();
    }

    public function chunk($size = 1, $preserveKeys = false)
    {
        $chunks = array();
        foreach (array_chunk($this->arr, $size, $preserveKeys) as $chunk) {
            $chunks[] = static::wrap($chunk);
        }
        return new static($chunks);
    }

    public function each($callback)
    {
        $i = 0;
        foreach ($this->arr as $item) {
            $callback($item, $i++);
        }
        return $this;
    }

    public function eachPair($callback)
    {
        foreach ($this->arr as $key => $value) {
            $callback($key, $value);
        }
        return $this;
    }

    public function filter($callback = null)
    {
        return new static(array_filter($this->arr, $callback));
    }

    public function tap($callback)
    {
        $callback($this);
        return $this;
    }

    public function tapRaw($callback)
    {
        $callback($this->arr);
        return $this;
    }

    public static function _map($array, $callback, $createKeys = false)
    {
        if ($createKeys) {
            $result = array();
            foreach (array_map($callback, $array) as $pair) {
                list($key, $value) = $pair;
                $result[$key] = $value;
            }
        } else {
            $result = array_map($callback, $array);
        }

        return $result;
    }

    public function map($callback, $createKeys = false)
    {
        return new static(static::_map($this->arr, $callback, $createKeys));
    }

    public static function _mapWithKey($array, $callback, $createKeys = false)
    {
        $mapped = array();
        if ($createKeys) {
            foreach ($array as $key => $value) {
                list($newKey, $newValue) = $callback($value, $key);
                $mapped[$newKey] = $newValue;
            }
        } else {
            foreach ($array as $key => $value) {
                $mapped[$key] = $callback($value, $key);
            }
        }

        return $mapped;
    }

    public function mapWithKey($callback, $createKeys = false)
    {
        return new static(static::_mapWithKey($this->arr, $callback, $createKeys));
    }

    public static function _flatMap($array, $callback)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $newValues = $callback($value, $key);
            if ($newValues) {
                foreach ($newValues as $newValue) {
                    $result[] = $newValue;
                }
            }
        }

        return $result;
    }


    public function flatMap($callback)
    {
        return new static(static::_flatMap($this->arr, $callback));
    }

    public static function _flatten($array)
    {
        return call_user_func_array('array_merge', $array);
    }

    public function flatten()
    {
        return new static(call_user_func_array('array_merge', $this->arr));
    }

    public static function _pluck($array, $valueAttribute, $keyAttribute = null, $arrayAccess = true)
    {
        $result = array();
        if ($arrayAccess) {
            foreach ($array as $key => $value) {
                $result[$keyAttribute ? $value[$keyAttribute] : $key] = $value[$valueAttribute];
            }
        } else {
            foreach ($array as $key => $value) {
                $result[$keyAttribute ? $value->{$keyAttribute} : $key] = $value->{$valueAttribute};
            }
        }

        return $result;
    }

    public function pluck($array, $valueAttribute, $keyAttribute = null, $arrayAccess = true)
    {
        return new static(static::_pluck($array, $valueAttribute, $keyAttribute, $arrayAccess));
    }

    public function fold($callback, $initial = null)
    {
        return array_reduce($this->arr, $callback, $initial);
    }

    public function foldRight($callback, $initial = null)
    {
        return array_reduce(array_reverse($this->arr), $callback, $initial);
    }

    public static function _partition($array, $predicate)
    {
        $pass = array();
        $fail = array();

        foreach ($array as $key => $value) {
            $predicate($value, $key)
                ? $pass[$key] = $value
                : $fail[$key] = $value;
        }

        return array($pass, $fail);
    }

    /**
     * @param $predicate
     * @return static[]
     */
    public function partition($predicate)
    {
        list($pass, $fail) = static::_partition($this->arr, $predicate);
        return array(new static($pass), new static($fail));
    }

    /**
     * Finds the smallest element
     *
     * @return mixed
     */
    public function min()
    {
        return min($this->arr);
    }

    /**
     * Finds the largest element
     *
     * @return mixed
     */
    public function max()
    {
        return max($this->arr);
    }

    /**
     * Finds the element for which the result of the callback is the smallest
     *
     * @param \callable $callback
     * @return mixed
     */
    public function minBy($callback)
    {
        $minResult = null;
        $minElement = null;
        foreach ($this->arr as $element) {
            $current = $callback($element);
            if (!isset($minResult) || $current < $minResult) {
                $minResult = $current;
                $minElement = $element;
            }
        }

        return $minElement;
    }

    /**
     * Finds the element for which the result of the callback is the largest
     *
     * @param \callable $callback
     * @return mixed
     */
    public function maxBy($callback)
    {
        $maxResult = null;
        $maxElement = null;
        foreach ($this->arr as $element) {
            $current = $callback($element);
            if (!isset($maxResult) || $current > $maxResult) {
                $maxResult = $current;
                $maxElement = $element;
            }
        }

        return $maxElement;
    }

    /**
     * Returns the sum of all elements
     *
     * @param \callable $callback If given, sums the results of this callback over each element
     * @return number
     */
    public function sum($callback = null)
    {
        if ($callback === null) {
            return array_sum($this->arr);
        }

        $sum = 0;
        foreach ($this->arr as $value) {
            $sum += $callback($value);
        }
        return $sum;
    }

    /**
     * @param array $array
     * @param array|Arr $arr
     * @param $callback
     * @return array
     */
    public static function _zipWith($array, $arr, $callback)
    {
        $result = array();
        foreach ($array as $a) {
            list(,$b) = each($arr);
            $result[] = $callback($a, $b);
        }

        return $result;
    }

    /**
     * @param array|Arr $arr
     * @param $callback
     * @return static
     */
    public function zipWith($arr, $callback)
    {
        $arr instanceof Arr and $arr = $arr->raw();
        return new static(static::_zipWith($this->arr, $arr, $callback));
    }

    /**
     * Sorts the array in-place
     *
     * @param bool $preserveKeys
     * @param int $mode Sort flags
     * @return static
     */
    public function sort($preserveKeys = false, $mode = SORT_REGULAR)
    {
        $preserveKeys ? asort($this->arr, $mode) : sort($this->arr, $mode);
        return $this;
    }

    /**
     * Returns a sorted copy of the array
     *
     * @param bool $preserveKeys
     * @param int $mode Sort flags
     * @return static
     */
    public function sorted($preserveKeys = false, $mode = SORT_REGULAR)
    {
        return $this->dup()->sort($preserveKeys, $mode);
    }

    /**
     * Sorts the array by a key or result of a callback
     *
     * @param array $array
     * @param string|\callback $callbackOrKey
     * @param int $mode Sort flags
     * @return array
     */
    public static function _sortBy($array, $callbackOrKey, $mode = SORT_REGULAR)
    {
        $sortBy = array();
        if (is_string($callbackOrKey)) {
            foreach ($array as $value) {
                $sortBy[] = $value[$callbackOrKey];
            }
        } else {
            foreach ($array as $key => $value) {
                $sortBy[] = $callbackOrKey($value, $key);
            }
        }

        array_multisort($sortBy, $mode, $array);

        return $array;
    }


    /**
     * Sorts the array in-place by a key or result of a callback
     *
     * @param string|\callback $callbackOrKey
     * @param int $mode Sort flags
     * @return static
     */
    public function sortBy($callbackOrKey, $mode = SORT_REGULAR)
    {
        return new static(static::_sortBy($this->arr, $callbackOrKey, $mode));
    }

    /**
     * Returns a copy of the array sorted by a key or result of a callback
     *
     * @param string|\callback $callbackOrKey
     * @param int $mode Sort flags
     * @return static
     */
    public function sortedBy($callbackOrKey, $mode = SORT_REGULAR)
    {
        return $this->dup()->sortBy($callbackOrKey, $mode);
    }


    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($this->arr, $offset);
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->arr[$offset];
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->arr[$offset] = $value;
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->arr[$offset]);
    }


    /**
     * Implementation of Iterator
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->arr);
    }

    /**
     * Implementation of Iterator
     */
    public function next()
    {
        next($this->arr);
    }

    /**
     * Implementation of Iterator
     *
     * @return int|string
     */
    public function key()
    {
        return key($this->arr);
    }

    /**
     * Implementation of Iterator
     *
     * @return boolean
     */
    public function valid()
    {
        return key($this->arr) === null;
    }

    /**
     * Implementation of Iterator
     */
    public function rewind()
    {
        reset($this->arr);
    }
}
