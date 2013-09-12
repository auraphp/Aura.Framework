<?php
namespace Aura\Framework\Mock;

use Aura\Web\Response;
use Aura\Framework\Web\Controller\AbstractPage;

class Page extends AbstractPage
{
    public function setView($view)
    {
        $this->view = $view;
    }
}
