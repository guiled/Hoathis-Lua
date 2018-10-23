<?php

namespace Hoathis\Lua;

/**
 * Description of Debugger
 *
 * @author houra
 */
class Debugger extends \Hoa\Compiler\Llk\Parser
{
    protected $debug        = '';
    protected $steps        = [];
    protected $currentToken = -1;
    protected $tokens       = [];

    public function showText(\Hoa\Compiler\Llk\Rule\Rule $r, $level = 0)
    {
        $result     = '';
        $childLines = [];

        $result .= $r->isTransitional() ? '' : ($r->getNodeId() ?? $r->getName()) . ($level === 0 ? ':' : '');

        $children = $r->getChildren();

        if ($r instanceof \Hoa\Compiler\Llk\Rule\Token) {
            if ($r->isKept()) {
                return '<' . $r->getTokenName() . '>';
            } else {
                return '::' . $r->getTokenName() . '::';
            }
        } elseif ($r instanceof \Hoa\Compiler\Llk\Rule\Concatenation) {
            //$childIndent = $r->isTransitional() ? '' : str_repeat('  ', $level + 1);
            $indent    = $r->isTransitional() ? '' : PHP_EOL . str_repeat('  ', $level + 1);
            $separator = $r->isTransitional() ? ' ' : PHP_EOL . str_repeat('  ', $level + 1);
            foreach ($children as $child) {
                $childRule = $this->_rules[$child];
                if (is_numeric($child)) {
                    $childLines[] = $this->showText($childRule, $level + 1);
                } else {
                    $childLines[] = $childRule->getName() . '()';
                }
            }
        } elseif ($r instanceof \Hoa\Compiler\Llk\Rule\Repetition) {
            $indent    = $separator = $r->isTransitional() ? '' : str_repeat('  ', $level + 1);
            $childRule = $this->_rules[$children];

            if ($r->getMin() == 0 && $r->getMax() == -1) {
                $symbol = '*';
            } elseif ($r->getMin() == 1 && $r->getMax() == -1) {
                $symbol = '+';
            } elseif ($r->getMin() == 0 && $r->getMax() == 1) {
                $symbol = '?';
            } else {
                $symbol = '{' . $r->getMin() . ',' . $r->getMax() . '}';
            }

            if (is_numeric($children)) {
                if ($childRule instanceof \Hoa\Compiler\Llk\Rule\Token) {
                    return $this->showText($childRule, $level + 1) . $symbol;
                } else {
                    return '( ' . $this->showText($childRule, $level + 1) . ' )' . $symbol;
                }
            } else {
                return $childRule->getName() . '()' . $symbol;
            }
        } elseif ($r instanceof \Hoa\Compiler\Llk\Rule\Choice) {
            $separator = ($r->isTransitional() ? ' ' : PHP_EOL . str_repeat('  ', $level + 1)) . '| ';
            $indent    = ($r->isTransitional() ? '' : PHP_EOL . str_repeat('  ', $level + 1));

            foreach ($children as $child) {
                $childRule = $this->_rules[$child];
                if (is_numeric($child)) {
                    $childLines[] = $this->showText($childRule, $level + 1);
                } else {
                    $childLines[] = $childRule->getName() . '()';
                }
            }
        }
        if (!empty($r->getNodeId()) && $r->isTransitional()) {
            $end = ' ' . $r->getNodeId();
        } else {
            $end = '';
        }
        return $result . $indent . implode($separator, $childLines) . $end;
    }

    public function showHtml(\Hoa\Compiler\Llk\Rule\Rule $r, $level = 0)
    {
        $childLines     = [];
        $childSeparator = PHP_EOL;

        $children = $r->getChildren();

        $classes = [];
        $id      = '';
        $content = $r->isTransitional() ? '' : ('<span class="name">' . ($r->getNodeId() ?? $r->getName()) . ($level === 0
                ? ':' : '') . '</span>');

        $classes[] = substr(strrchr(get_class($r), '\\'), 1);
        if ($r->isTransitional()) {
            $classes[] = 'Transitional';
        }

        $id = $r->getName();

        if ($r instanceof \Hoa\Compiler\Llk\Rule\Token) {
            if ($r->isKept()) {
                $content = htmlentities('<' . $r->getTokenName() . '>');
            } else {
                $content = '::' . $r->getTokenName() . '::';
            }
        } elseif ($level > 0 && !is_numeric($r->getName())) {
            $content = $r->getName() . '()';
        } elseif ($r instanceof \Hoa\Compiler\Llk\Rule\Concatenation) {
            //$childIndent = $r->isTransitional() ? '' : str_repeat('  ', $level + 1);
            foreach ($children as $child) {
                $childLines[] = $this->showHtml($this->_rules[$child], $level + 1);
            }
        } elseif ($r instanceof \Hoa\Compiler\Llk\Rule\Repetition) {
            $indent    = $separator = $r->isTransitional() ? '' : str_repeat('  ', $level + 1);
            $childRule = $this->_rules[$children];

            if ($r->getMin() == 0 && $r->getMax() == -1) {
                $symbol = '*';
            } elseif ($r->getMin() == 1 && $r->getMax() == -1) {
                $symbol = '+';
            } elseif ($r->getMin() == 0 && $r->getMax() == 1) {
                $symbol = '?';
            } else {
                $symbol = '{' . $r->getMin() . ',' . $r->getMax() . '}';
            }

            //if (is_numeric($children)) {
            if ($childRule instanceof \Hoa\Compiler\Llk\Rule\Token || $childRule instanceof \Hoa\Compiler\Llk\Rule\Choice) {
                $childLines[] = $this->showHtml($childRule, $level + 1);
                $childLines[] = $symbol;
                //return '<div class="Repetition" id="' . $r->getName() . '">' . $this->showHtml($childRule, $level + 1) . $symbol . "</div>";
            } else {
                $childLines[] = '<div class="Group Group_">(</div>';
                $childLines[] = $this->showHtml($childRule, $level + 1);
                $childLines[] = '<div class="Group _Group">)</div><div class="Quantifier">' . $symbol . '</div>';
                //return '<div class="Repetition" id="' . $r->getName() . '">' . '( ' . $this->showHtml($childRule, $level + 1) . ' )' . $symbol . "</div>";
            }

            //} else {
            //    $content = $childRule->getName() . '()' . $symbol;
            //return '<div class="Repetition" id="' . $r->getName() . '">' . $childRule->getName() . '()' . $symbol . "</div>";
            //}
            //return '<div class="' . implode(' ', $classes) . '" id="' . $id . '"' . htmlentities($content) . '</div>';
        } elseif ($r instanceof \Hoa\Compiler\Llk\Rule\Choice) {
            if ($r->isTransitional()) {
                $childLines[] = '<div class="Group Group_">(</div>';
            }
            foreach ($children as $numChild => $child) {
                $childRule    = $this->_rules[$child];
                //if (is_numeric($child)) {
                $childLines[] = ($numChild > 0 ? '<div class="Alternation Transitional"><span class="or">|</span>' : '') . $this->showHtml($childRule,
                        $level + 1) . ($numChild > 0 ? '</div>' : '');
                //} else {
                //    $childLines[] = '<div class="" id="' . $childRule->getName() . '">' . $childRule->getName() . '()' . "</div>";
                //}
            }
            if ($r->isTransitional()) {
                $childLines[] = '<div class="Group _Group">)</div>';
            }
        }
        if (!empty($r->getNodeId()) && $r->isTransitional()) {
            $end = ' ' . $r->getNodeId();
        } else {
            $end = '';
        }
        //$content = $r->isTransitional() ? '' : ('<span class="name">' . ($r->getNodeId() ?? $r->getName()) . ($level === 0 ? ':' : '') . '</span>') . implode(PHP_EOL, $childLines);
        return '<div class="' . implode(' ', $classes) . '" id="' . $id . '">' . $content . implode($childSeparator,
                $childLines) . $end . '</div>';
    }

    public function __construct(array $tokens = array(), array $rules = array(), array $pragmas = array())
    {
        parent::__construct($tokens, $rules, $pragmas);

        //$ruleParents = [];
        //foreach ($rules as $rule) {
        //    if (!is_numeric($rule->getName())) {
        //echo $rule->getName(), PHP_EOL;
        //    }
        /* if ($rule instanceof \Hoa\Compiler\Llk\Rule\Concatenation) {
          foreach ($rule->getChildren() as $childId) {
          $ruleParents[$childId][] = $ruleName;
          }
          } */
        //}
        /* @var $r \Hoa\Compiler\Llk\Rule */
        foreach ($rules as $r) {
            if (!($r instanceof \Hoa\Compiler\Llk\Rule\Token) && !is_numeric($r->getName())) {
                $this->debug .= $this->showHtml($r);
            }

            /* if (!($r instanceof \Hoa\Compiler\Llk\Rule\Token) && !is_numeric($r->getName())) {
              echo $r->getNodeId() ?? $r->getName(), PHP_EOL;//,
              $children = $r->getChildren();
              foreach ($children as $child) {
              if (is_numeric($child)) {
              var_dump($rules[$child]);
              } else {
              echo "\t", $rules[$child]->getName(), '()', PHP_EOL, PHP_EOL;
              }
              }
              } */
            //echo $r->getName(), ' ', substr(strrchr(get_class($r), '\\'), 1), ' ', $r->getNodeId(), ' ', ($r instanceof \Hoa\Compiler\Llk\Rule\Token
            //        ? $r->getTokenName() : '[' . implode(',', $r->getChildren()) . ']'), ' ', $r->getPPRepresentation(), PHP_EOL;
        }
    }

    public function parse($text, $rule = null, $tree = true)
    {
        $result = parent::parse($text, $rule, $tree);

        /* foreach ($this->_rules as $rule) {
          var_dump($rule);
          }
          exit(); */
        $this->tokens[$this->currentToken]['time'] = microtime(true) - $this->tokens[$this->currentToken]['time'];
        echo $this->getDebug(), PHP_EOL;

        return $result;
    }

    protected function _parse(\Hoa\Compiler\Llk\Rule\Rule $zeRule, $next)
    {
        if ($this->currentToken !== $this->_tokenSequence->key()) {
            if ($this->currentToken > -1) {
                $this->tokens[$this->currentToken]['duration'] += microtime(true) - $this->tokens[$this->currentToken]['time'];
                $this->tokens[$this->currentToken]['time'] = null;
                $this->tokens[$this->currentToken]['steps'][] = count($this->steps) - 1;

            }
            $this->currentToken = $this->_tokenSequence->key();
            if (!isset($this->tokens[$this->currentToken])) {
                $this->tokens[$this->currentToken]          = $this->_tokenSequence->current();
                $this->tokens[$this->currentToken]['stepscnt'] = 0;
                $this->tokens[$this->currentToken]['duration'] = 0;
                $this->tokens[$this->currentToken]['time']  = microtime(true);
                $this->tokens[$this->currentToken]['steps'] = [];
            }
        }
        $this->tokens[$this->currentToken]['stepscnt'] ++;
        $result = parent::_parse($zeRule, $next);

        $this->steps[] = $zeRule->getName();


        //echo ($result ? 'OK ' : '   '), get_class($zeRule), ' ', $zeRule->getName(), ' ', substr($zeRule->getPPRepresentation(),
        //        0, 30), PHP_EOL;

        return $result;
    }

    public function getDebug()
    {
        $head   = <<<EOF
<html>
    <head>
        <style>
        div:not(.Transitional) > div {
            margin-left: 20px;
        }

        div.Transitional > div, div.Group {
            display: inline-block;
        }

        .or {
            margin: 0 10px 0 10px;
        }
        </style>
    </head>
<body>
EOF;
        $js     = <<<EOF
            var i = -1, delay = 200;
            function hl() {
            if (i >= 0) {
                document.getElementById(steps[i]).style.background = 'transparent';
            }
            i++;
            if (i < steps.length - 1) {
                document.getElementById(steps[i]).style.background = 'orange';
                setTimeout(hl, delay);
            }
   }
    setTimeout(hl, delay);

EOF;
        $script = '</body><script type="text/javascript">var steps=' . json_encode($this->steps, JSON_PRETTY_PRINT) . ';' . $js . '</script></html>';
        $total  = 0;
        foreach ($this->tokens as $t) {
            $total += $t['stepscnt'];
            echo $t['token'], ' (', $t['value'], ') ', $t['stepscnt'], ' ', number_format($t['time'], 4), PHP_EOL;
        }
        echo 'total : ', $total, PHP_EOL;
        echo 'Rules : ', count($this->steps);
        return $head . $this->debug . $script;
    }
}