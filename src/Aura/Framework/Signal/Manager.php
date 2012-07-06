<?php
namespace Aura\Framework\Signal;

use Aura\Signal\Manager as SignalManager;
use Aura\Web\SignalInterface as WebSignalInterface;

class Manager extends SignalManager implements WebSignalInterface
{
    // nothing to do but implement the separated interface
}
