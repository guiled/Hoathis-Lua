<?php
namespace Hoathis\Lua\Visitor\Node;

/**
 * Description of Assignation
 *
 * @author Guislain Duthieuw
 */
class Assignation extends \Hoathis\Lua\Visitor\Node
{

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();

        $leftVar  = $children[0]->accept($this->interpreter, $handle, $eldnah);
        $rightVar = $children[1]->accept($this->interpreter, $handle, $eldnah);
        $this->interpreter->getEnvironment()->set($leftVar, $rightVar);
        /*if ($leftVar instanceof \Hoathis\Lua\Model\ValueGroup) {
            $symbols = $leftVar->getValue();
        } else {
            $symbols = array($leftVar);
        }

        if ($rightVar instanceof \Hoathis\Lua\Model\ValueGroup) {
            $values = $rightVar->getValue();
        } else {
            $values = array($rightVar);
        }*/
        //$this->interpreter->setValueGroupToValueGroup($symbols, $values, $assignation_local);
    }
}