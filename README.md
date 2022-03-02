# Boollet

Boollet is a boolean algebra toolkit for PHP.

[![PHP Version Require](http://poser.pugx.org/d3lph1/boollet/require/php)](https://packagist.org/packages/d3lph1/boollet)
[![License](http://poser.pugx.org/d3lph1/boollet/license)](https://packagist.org/packages/d3lph1/boollet)

## Features

- [x] Expression object API
- [x] Expression parser
- [x] Building truth tables
- [x] Complete conjunctive/disjunctive normal form calculation
- [x] Zhegalkin Polynomial calculation
- [x] SAT and UNSAT solvers

## Requirements

* PHP >= 8.1

## Installation

```bash
composer require d3lph1/boollet
```

## Usage
### Expression object API

You can create either `UnaryExpression` or `BinaryExpression` with one or two operands respectively:

```php
use D3lph1\Boollet\Structure\Expression\{Variable, UnaryExpression, BinaryExpression};
use D3lph1\Boollet\Structure\Operator\{UnaryOperators, BinaryOperators};

$expr = new BinaryExpression(
    new UnaryExpression(UnaryOperators::NOT, new Variable(false)),
    BinaryOperators::AND,
    new BinaryExpression(new Variable(true), BinaryOperators::OR, new Variable(false, label: 'Z'))
);

echo $expr; // (!A ⋀ (B ⋁ Z))
```

> If there is no label for variable specified, it will be assigned with sequentially autogenerated symbols. 

Evaluate the expression with initial variable values:

```php
$val = $expr->evaluate(); // true
```

Evaluate the expression with overwritten variable values (It can be partially overwritten): 

```php
$val = $expr->evaluate(['A' => true, 'B' => true, 'Z' => true]) // false
```

### Value binding

In the example above there are only static variable values which could not be changed dynamically without expression reconstructing.

To change value of variable at runtime you should use `Variable::set()` method. For convenient batch value setup there is `ValueBinder` class:

```php
use D3lph1\Boollet\ValueBinder;

$a = new Variable(false);
$b = new Variable(true);
$z = new Variable(false, label: 'Z');

$expr = new BinaryExpression(
    new UnaryExpression(UnaryOperators::NOT, $a),
    BinaryOperators::AND,
    new BinaryExpression($b, BinaryOperators::OR, $z)
);

$binder = new ValueBinder();
$binder->bind($a);
$binder->bindAll([$b, $z]);

$binder->set([
    'A' => true,
    'B' => true,
    'Z' => true
])

$expr->evaluate(); // true
```

### Expression parser

For parsing stringed expressions uses `ShuntingYardParser` parser implementation. Under the hood it uses Dijkstra's algorithm of the same name.

```php
use D3lph1\Boollet\Parser\{Lexer, Reader\StringInputReader, ShuntingYardParser};

$lexer = Lexer::default();
$input = new StringInputReader('X ⊕ Y → (X ⋀ Z)');
$parser = new ShuntingYardParser($lexer);

$expr = $parser->parse($input);

echo $expr; // ((X ⊕ Y) → (X ⋀ Z))
```

### Building truth table

```php
use D3lph1\Boollet\TruthTable;

$table = TruthTable::tabulate($expr);
$table->setLabel('f(X ⊕ Y → (X ⋀ Z))');

echo $table;
```

```
+---+---+---+--------------------+
| X | Y | Z | f(X ⊕ Y → (X ⋀ Z)) |
+---+---+---+--------------------+
| 0 | 0 | 0 |                  1 |
| 0 | 0 | 0 |                  1 |
| 0 | 1 | 0 |                  0 |
| 0 | 1 | 0 |                  0 |
| 1 | 0 | 0 |                  0 |
| 1 | 0 | 0 |                  1 |
| 1 | 1 | 0 |                  1 |
| 1 | 1 | 0 |                  1 |
+---+---+---+--------------------+
```

### Complete conjunctive/disjunctive normal form calculation

Class `NormalForms` provides utility methods to find complete conjunctive (or disjunctive ) normal form representations.

```php
use D3lph1\Boollet\NormalForm\NormalForms;

// $expr ~ ((X ⊕ Y) → (X ⋀ Z))

$ccnf = NormalForms::calculateCompleteConjunctive($expr); // ((X ⋁ (!Y ⋁ Z)) ⋀ ((X ⋁ (!Y ⋁ !Z)) ⋀ (!X ⋁ (Y ⋁ Z))))
$cdnf = NormalForms::calculateCompleteDisjunctive($expr); // ((!X ⋀ (!Y ⋀ !Z)) ⋁ ((!X ⋀ (!Y ⋀ Z)) ⋁ ((X ⋀ (!Y ⋀ Z)) ⋁ ((X ⋀ (Y ⋀ !Z)) ⋁ (X ⋀ (Y ⋀ Z))))))
```

### Zhegalkin Polynomial calculation

For such needs you can use `ZhegalkinPolynomial` utility class:

```php
use \D3lph1\Boollet\ZhegalkinPolynomial;

// $expr ~ (!X → ((!Y ⊕ X) ⋀ !Z))

$polynomial = ZhegalkinPolynomial::calculate($expr);

echo $polynomial; // ((Z ⋀ (Y ⋀ X)) ⊕ ((Y ⋀ X) ⊕ ((Z ⋀ X) ⊕ ((Z ⋀ Y) ⊕ (Y ⊕ (Z ⊕ 1))))))
```

### SAT and UNSAT solvers

Boollet provides naive algorithm implementations to solve [boolean (un)satisfiability problem](https://en.wikipedia.org/wiki/Boolean_satisfiability_problem).


 > SAT is the problem of determining if there exists an interpretation that satisfies a given boolean formula (formula becomes `true`).

 > UNSAT is the problem of determining if there exists an interpretation that not satisfies a given boolean formula (formula becomes `false`). 

`CompleteDisjunctiveNormalFormSATSolver` works only with expressions in complete disjunctive normal form. Whereas `CompleteConjunctiveNormalFormUNSATSolver` uses only expressions in complete conjunctive normal form.

The second argument of the method `findAllPossibleSolutions()` takes an array of variables with respect to which it is required to solve the problem.
Other variables whose labels are not passed to this argument must have values (in the example below `y` is such variable).

```php
use \D3lph1\Boollet\SAT\CompleteDisjunctiveNormalFormSATSolver;
// $expr ~ X ⋁ (Y ⋀ Z)

$y->set(false);

$cdnf = NormalForms::calculateCompleteDisjunctive($expr);

$sat = new CompleteDisjunctiveNormalFormSATSolver();
$solutions = $sat->findAllPossibleSolutions($cdnf, ['X', 'Z']);
$solutions = $sat->findAllPossibleSolutions($cdnf, ['X', 'Z']);
```

`$solutions` will look like this:

```php
^ array:2 [▼
  0 => array:2 [▼
    "X" => true
    "Z" => false
  ]
  1 => array:2 [▼
    "X" => true
    "Z" => true
  ]
]
```

To conveniently define results constraints, you can use `findAllPossibleSolutionsWithConstraints()`:

```php
use D3lph1\Boollet\Constraints\Constraints;

$solutions = $sat->findAllPossibleSolutionsWithConstraints($cdnf, ['X', 'Z'], new class() implements Constraints {
    public function isSatisfy(array $values): bool
    {
        return $values['X'];
    }
});
```

`$solutions` will look like this:

```php
^ array:1 [▼
  0 => array:2 [▼
    "X" => true
    "Z" => false
  ]
]
```

## License

This code is published under the [MIT license](https://opensource.org/licenses/MIT). This means you can do almost anything with it, as long as the copyright notice and the accompanying license file is left intact.
