<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputGroup extends Component
{
    public $label;
    public $name;
    public $value;
    public $type;

    public function __construct($label = '', $name = '', $value = '', $type = 'text')
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.input-group');
    }
}
