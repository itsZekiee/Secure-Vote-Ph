<?php
// File: `app/View/Components/DefaultHeader.php`
namespace App\View\Components;

use Illuminate\View\Component;

class DefaultHeader extends Component
{
    public string $title;

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('components.default-header');
    }
}
