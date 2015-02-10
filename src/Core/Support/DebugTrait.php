<?php
/** {license_text}  */

/** {license_text}  */
namespace Core\Support;

use Barryvdh\Debugbar\LaravelDebugbar;

trait DebugTrait
{
    /** @var  LaravelDebugbar */
    protected $debugBar;

    /**
     * @param LaravelDebugbar $debugBar
     */
    public function setDebugBar(LaravelDebugbar $debugBar)
    {
        $this->debugBar = ($debugBar->isEnabled() ? $debugBar : false);
    }

    /**
     * @param string $name
     * @param null $label
     */
    protected function startDebugMeasure($name, $label = null)
    {
        if ($this->debugBar) {
            $this->debugBar->startMeasure($name, $label);
        }
    }

    /**
     * @param string $label
     * @param int $start
     * @param int $end
     */
    protected function addDebugMeasure($label, $start, $end)
    {
        if ($this->debugBar) {
            $this->debugBar->addMeasure($label, $start, $end);
        }
    }

    /**
     * @param string $name
     */
    protected function stopDebugMeasure($name)
    {
        if ($this->debugBar) {
            $this->debugBar->stopMeasure($name);
        }
    }
}
