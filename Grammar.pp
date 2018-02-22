//
// Hoa
//
//
// @license
//
// New BSD License
//
// Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//     * Redistributions of source code must retain the above copyright
//       notice, this list of conditions and the following disclaimer.
//     * Redistributions in binary form must reproduce the above copyright
//       notice, this list of conditions and the following disclaimer in the
//       documentation and/or other materials provided with the distribution.
//     * Neither the name of the Hoa nor the names of its contributors may be
//       used to endorse or promote products derived from this software without
//       specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
// AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
// IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
// ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
// LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
// CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
// SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
// INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
// CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
// POSSIBILITY OF SUCH DAMAGE.
//
// Grammar \Hoathis\Lua\Grammar.
//
// Provide grammar for Lua.
//
// @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
// @copyright  Copyright © 2007-2013 Ivan Enderlin.
// @license    New BSD License
//

%skip   blank         [\s\n]+
%skip   block_comment \-\-\[\[(.|\n)*?\-\-\]\]
%skip   comment       \-\-[^\n]*

// Keywords.
%token  and           and(?=\W)
%token  break         break(?=\W)
%token  do            do(?=\W)
%token  elseif        elseif(?=\W)
%token  else          else(?=\W)
%token  end           end(?=\W)
%token  false         false(?=\W|$)
%token  for           for(?=\W)
%token  function      function(?=\W)
%token  goto          goto(?=\W)
%token  if            if(?=\W)
%token  in            in(?=\W)
%token  local         local(?=\W)
%token  nil           nil(?=\W)
%token  not           not(?=\W)
%token  or            or(?=\W)
%token  repeat        repeat(?=\W)
%token  return        return(?=\W)
%token  then          then(?=\W)
%token  true          true(?=\W|$)
%token  until         until(?=\W)
%token  while         while(?=\W)

%token  string        (["'])(.*?)(?<!\\\\)\1
%token  longstring    \[(=*)\[((?:.|\n)*?)\]\1\]

// Operators.
%token  plus          \+
%token  minus         \-
%token  times         \*
%token  idiv          //
%token  div           /
%token  modulo        %
%token  pow           \^
%token  band          &
%token  bor           \|
%token  bnot          ~
%token  shl           <<
%token  shr           >>
%token  length        #
%token  dequal        ==
%token  nequal        ~=
%token  lte           <=
%token  gte           >=
%token  lt            <
%token  gt            >
%token  equal         =
%token  parenthesis_  \(
%token _parenthesis   \)
%token  brace_        \{
%token _brace         \}
%token  bracket_      \[
%token _bracket       \]
%token  dcolon        ::
%token  semicolon     ;
%token  colon         :
%token  comma         ,
%token  tpoint        \.\.\.
%token  dpoint        \.\.
%token  point         \.

// Values.
%token  number        [\-+]?(0|[1-9]\d*)(\.\d+)?([eE][\+\-]?\d+)?

// Misc.

// Identifier.
%token  identifier    [a-zA-Z_]([\w\d_]+)?


chunk:
    block()

#block:
	block_code()

block_code:
    statement()* return_statement()?

statement:
    ::semicolon::
  | variables() ::equal:: expressions() #assignation
  | function_call()
  | label()
  | ::goto:: <identifier> #goto
  | ::do:: block_code() ::end:: #do_block
  | ::while:: expression() ::do:: block() ::end:: #while_loop
  | ::repeat:: block() ::until:: expression() #do_while_loop
  |   ::if::   expression() ::then:: block()
    ( <elseif> expression() ::then:: block() )*
    ( <else>   block() )? ::end:: #if
  | ::for:: <identifier> ::equal::
    expression() ::comma:: expression() ( ::comma:: expression() )?
    ::do:: block() ::end:: #for_loop
  | ::for:: names() ::in:: expressions() ::do:: block() ::end:: #for_in_loop
  | ::function:: function_name() function_body() #function
  | ::local:: ::function:: <identifier> function_body() #local_function
  | ::local:: names() ( ::equal:: expressions() ) #assignation_local

return_statement:
    ::return:: expressions()? ::semicolon::? #return
  | ::break:: ::semicolon::? #break

#label:
    ::dcolon:: <identifier> ::dcolon::

function_name:
    <identifier> ( ::point:: <identifier> )*
    ( ::colon:: <identifier> )?

variables:  
    variable() ( ::comma:: variable() )*

variable:
    <identifier>
  | ::parenthesis_:: expression() ::_parenthesis:: #onlyfirst
  | (
        <identifier>
      | function_call()
      | ::parenthesis_:: expression() ::_parenthesis::
    )
    (
        ::bracket_:: expression() ::_bracket:: #table_access
      | ::point:: <identifier> #table_access
    )+

names:
    <identifier> ( ::comma:: <identifier> )*

expressions:
    expression() ( ::comma:: expression() )*

expression:
    expression_quaternary()
//    expression_primary()
//    ( ::or:: expression() #or )?

//expression_primary:
//    expression_secondary()
//    ( ::and:: expression() #and )?

//expression_secondary:
//    expression_tertiary()
//    ( ( <lt> | <gt> | <lte> | <gte> | <nequal> | <dequal> )
//      expression() #comparison )?

//expression_tertiary:
//    expression_quaternary()
//    ( ::dpoint:: expression() #concatenation )?

expression_quaternary:
    expression_quinary()
//    ( ( ::plus:: #addition | ::minus:: #substraction ) expression() )?
    ( ::plus:: #addition expression() )?

expression_quinary:
    expression_term()
    (  ::times:: #multiplication 
//    ( ( ::times:: #multiplication | ::div:: #division | ::idiv:: #floordivision | ::modulo:: #modulo )
      expression() )?

expression_senary:
    expression_term()
    ( ( ::not:: #not | ::length:: #length | ::pow:: #power )
      expression() )?

expression_term:
    ( ::pow:: #power ) expression()
  | <nil>
  | <false>
  | <true>
  | (::minus:: #negative | ::plus:: | ::pow:: #power)? <number>
  | <longstring>
  | <string>
  | <tpoint>
  | variable()
  | function_call()
  | function_definition()
  | table_constructor()

#function_call:
    ( <identifier> | ::parenthesis_:: expression() ::_parenthesis:: )
    (
        ::bracket_:: expression() ::_bracket:: #table_access
      | ::point:: ( <identifier> | function_call() ) #table_access
    )*
    ( ::colon:: <identifier> )? arguments()

#arguments:
    ::parenthesis_:: expressions()? ::_parenthesis::
//  | table_constructor()
//  | <string>
//  | <longstring>

function_definition:
    ::function:: function_body() #function_lambda

function_body:
    ::parenthesis_:: parameters()? ::_parenthesis:: _function_body() ::end::

_function_body:
    block() #function_body

#parameters:
    names() ( ::comma:: ::tpoint:: )?
  | ::tpoint::

table_constructor:
    ::brace_:: fields()? ::_brace:: #table

fields:
    field() ( ( ::comma:: | ::semicolon:: ) field() )*
    ( ::comma:: | ::semicolon:: )?

#field:
    ::bracket_:: expression() ::_bracket:: ::equal:: expression()
  | <identifier> ::equal:: expression()
  | expression()
