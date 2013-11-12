About
=======
Provides data validation against filters and constraints.

Example
-------

```php
include 'src/CBC/Data/Validation/Validator.php';
include 'src/CBC/Data/Validation/Constraint.php';
include 'src/CBC/Data/Validation/Filter.php';

use CBC\Data\Validation\Validator;
use CBC\Data\Validation\Constraint;
use CBC\Data\Validation\Filter;

$validator = new Validator();
$validator
	->setRule('name')
	->addConstraint(Constraint::required(), 'Name is required.')
	->setRule('email')
	->addConstraint(Constraint::required(), 'Email is required.')
	->addConstraint(Constraint::email(), 'Invalid Email Address.')
	->setRule('state')
	->addFilter(Filter::upper())
	->addConstraint(is_state(), 'Invalid State.')
;

$request = [
	'name' => 'Cameron Condry',
	'email' => 'invalid_email',
	'state' => 'in',
];

var_dump($validator->is_valid($request));
var_dump($validator->hasError('email'));
var_dump($validator->getError('email'));

function is_state() {
	return function ($data) {
		return in_array($data, ['IN', 'OK', 'NY']);
	};
}
```