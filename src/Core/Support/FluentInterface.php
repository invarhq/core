<?php
/** {license_text}  */
namespace Core\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

interface FluentInterface
    extends \ArrayAccess, Arrayable, Jsonable, \JsonSerializable
{
    public function setAttributes($attributes = array());
}
