<?php
/** {license_text}  */
namespace Core\Support;

class Fluent
    implements FluentInterface
{
    use FluentTrait;
    
    public function __construct(array $attributes = null)
    {
        if ($attributes) {
            $this->setAttributes($attributes);
        }
    }
}
