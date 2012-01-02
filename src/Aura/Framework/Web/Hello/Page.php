<?php
namespace Aura\Framework\Web\Hello;
use Aura\Framework\Web\AbstractPage;
class Page extends AbstractPage
{
    public function actionWorld()
    {
        $this->view->setInnerView('world');
    }
    
    public function actionAsset()
    {
        $this->view->setInnerView('asset');
    }
}
