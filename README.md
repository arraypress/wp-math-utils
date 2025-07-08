# WordPress Math Utils

A lean WordPress library for mathematical calculations, e-commerce operations, number utilities, and safe expression evaluation.

## Installation

```bash
composer require arraypress/wp-math-utils
```

## Quick Start

```php
use ArrayPress\MathUtils\Math;
use ArrayPress\MathUtils\Number;

// E-commerce calculations
$discount = Math::discount(100, 20, true);        // 20% off: ['discount' => 20, 'final_price' => 80]
$tax = Math::tax(100, 8.25, false);               // Add tax: ['tax' => 8.25, 'price_with_tax' => 108.25]

// Business metrics
$margin = Math::profit_margin(120, 100);          // 16.67% profit margin
$change = Math::percentage_change(100, 120);      // 20% increase

// Number formatting
$formatted = Number::format(1234.56);             // "1,234.56"
$ordinal = Number::ordinal(23);                   // "23rd"
$abbreviated = Number::abbreviate(1500000);       // "1.5M"

// Safe expression evaluation
$result = wp_evaluate_expression('2 + 3 * 4');    // 14
$user_calc = wp_safe_eval($_POST['formula']);      // Safe alternative to eval()
```

## Math Class

### `percentage(float $value, float $percentage, int $precision = 2): float`
Calculate percentage of a value.

```php
$result = Math::percentage( 100, 25 ); // 25.00 (25% of 100)
$result = Math::percentage( 200, 15 ); // 30.00 (15% of 200)
```

### `discount(float $price, float $discount, bool $is_percentage = true, int $precision = 2): array`
Apply discount (percentage or flat amount).

```php
// Percentage discount
$result = Math::discount( 100, 20, true );
// Returns: ['discount' => 20.00, 'final_price' => 80.00]

// Flat discount  
$result = Math::discount( 100, 15, false );
// Returns: ['discount' => 15.00, 'final_price' => 85.00]
```

### `tax(float $price, float $rate, bool $inclusive = false, int $precision = 2): array`
Calculate tax (inclusive or exclusive).

```php
// Exclusive tax (add to price)
$result = Math::tax( 100, 8.25, false );
// Returns: ['tax' => 8.25, 'price_with_tax' => 108.25]

// Inclusive tax (extract from price)
$result = Math::tax( 108.25, 8.25, true );
// Returns: ['tax' => 8.25, 'price_without_tax' => 100.00]
```

### `profit_margin(float $revenue, float $cost, int $precision = 2): float`
Calculate profit margin as percentage.

```php
$margin = Math::profit_margin( 120, 100 ); // 16.67% margin
$margin = Math::profit_margin( 150, 100 ); // 33.33% margin
```

### `percentage_change(float $old_value, float $new_value, int $precision = 2): float`
Calculate percentage change between values.

```php
$change = Math::percentage_change( 100, 120 ); // 20.00 (20% increase)
$change = Math::percentage_change( 120, 100 ); // -16.67 (16.67% decrease)
```

### `conversion_rate(float $conversions, float $total, int $precision = 2): float`
Calculate conversion rate as percentage.

```php
$rate = Math::conversion_rate( 25, 1000 ); // 2.5% conversion rate
$rate = Math::conversion_rate( 5, 100 );   // 5% conversion rate
```

### `average(array $values, int $precision = 2): float`
Calculate average of array values.

```php
$avg = Math::average( [10, 20, 30] );      // 20.00
$avg = Math::average( [100, 150, 200] );   // 150.00
```

### Currency Precision
```php
// Convert to/from cents for precise calculations
$cents  = Math::to_cents( 19.99 );  // 1999
$amount = Math::from_cents( 1999 );  // 19.99
```

### Utility Functions
```php
// Ensure non-negative values
$positive = Math::positive( -5 );      // 0.00

// Clamp to range
$clamped = Math::clamp( 150, 0, 100 );  // 100.00
```

## Number Class

### Validation
```php
Number::is_even( 4 );               // true
Number::is_odd( 5 );                // true
Number::is_positive( 10 );          // true
Number::is_negative( -5 );          // true
Number::in_range( 5, 1, 10 );       // true
```

### Formatting
```php
// Format with thousands separator
Number::format( 1234.56 );         // "1,234.56"
Number::format( 1234.56, 1 );      // "1,234.6"

// Ordinal numbers
Number::ordinal( 1 );               // "1st"
Number::ordinal( 22 );              // "22nd" 
Number::ordinal( 103 );             // "103rd"

// Zero padding
Number::zero_pad( 5, 3 );           // "005"
Number::zero_pad( 42, 6 );          // "000042"

// Abbreviate large numbers
Number::abbreviate( 1500 );         // "1.5K"
Number::abbreviate( 2500000 );      // "2.5M"
Number::abbreviate( 1200000000 );   // "1.2B"
```

### Advanced
```php
// Round to step
Number::round_to_step( 23.7, 5 );   // 25.0 (nearest 5)
Number::round_to_step( 12.3, 0.5 ); // 12.5 (nearest 0.5)

// Convert values with constraints
Number::to_numeric( '42', 'int', 0, 100 );  // 42 (integer, clamped to 0-100)
Number::to_numeric( '3.14159', 'float' );   // 3.14159
```

## Expression Parser

Safe evaluation of mathematical expressions - a secure alternative to `eval()`.

### Basic Usage
```php
use ArrayPress\MathUtils\ExpressionParser;

$parser = new ExpressionParser( 2 ); // 2 decimal places

// Basic arithmetic
$result = $parser->evaluate( '2 + 3 * 4' );        // 14
$result = $parser->evaluate( '(10 + 5) / 3' );     // 5
$result = $parser->evaluate( '2 ^ 3 ^ 2' );        // 512 (right associative)

// Decimal numbers
$result = $parser->evaluate( '3.14 * 2' );         // 6.28
$result = $parser->evaluate( '10.5 / 2.1' );       // 5
```

### Helper Functions
```php
// Simple evaluation
$result = wp_evaluate_expression( '100 * 1.25 + 50' );     // 175
$result = wp_evaluate_expression( '15.99 * 0.85', 4 );     // 13.5915 (4 decimal places)

// Safe eval alternative
$user_formula = $_POST['calculation']; // User input: "price * quantity + shipping"
$result = wp_safe_eval( $user_formula );

// Error handling
$result = wp_evaluate_expression( 'invalid expression' );
if ( $result === null ) {
    echo 'Invalid mathematical expression';
}
```

### Security Features
- **No code execution** - Only mathematical operations allowed
- **Input validation** - Rejects invalid characters and syntax
- **Safe operators** - Only `+`, `-`, `*`, `/`, `^`, `(`, `)` and numbers
- **Error handling** - Graceful failure instead of PHP errors

## Real-World Examples

### Dynamic Pricing Formulas
```php
// Admin sets pricing formula
$formula = get_option( 'pricing_formula' ); // "(base_price * quantity) + shipping_cost"

// Calculate price based on user input
$base_price = 25.99;
$quantity = 3;
$shipping = 5.99;

$expression = str_replace( 
    ['base_price', 'quantity', 'shipping_cost'], 
    [$base_price, $quantity, $shipping], 
    $formula 
);

$total = wp_evaluate_expression( $expression ); // 83.96
```

### User Calculator Widget
```php
// Safe user calculator
if ( isset( $_POST['calculation'] ) ) {
    $result = wp_safe_eval( sanitize_text_field( $_POST['calculation'] ) );
    
    if ( $result !== null ) {
        echo "Result: " . Number::format( $result );
    } else {
        echo "Invalid expression. Please use only numbers and +, -, *, /, ^ operators.";
    }
}
```

### E-commerce Checkout
```php
$item_price = 99.99;

// Apply 20% off coupon
$discount_result  = Math::discount( $item_price, 20, true );
$discounted_price = $discount_result['final_price']; // 79.99

// Add 8.25% tax
$tax_result  = Math::tax( $discounted_price, 8.25, false );
$final_total = $tax_result['price_with_tax']; // 86.59

// Display formatted
echo "Original: $" . Number::format( $item_price );                   // $99.99
echo "Discount: -$" . Number::format( $discount_result['discount'] ); // -$20.00
echo "Tax: $" . Number::format( $tax_result['tax'] );                 // $6.60
echo "Total: $" . Number::format( $final_total );                    // $86.59
```

### Business Analytics
```php
// Monthly revenue analysis
$last_month = 45000;
$this_month = 52000;

$change           = Math::percentage_change( $last_month, $this_month );
$formatted_change = Number::format( $change, 1 ) . '%';

echo "Revenue growth: " . $formatted_change; // "Revenue growth: 15.6%"

// Commission calculations using expressions
$sales_data = [
    ['amount' => 1200, 'rate' => 5],
    ['amount' => 850, 'rate' => 7.5],
    ['amount' => 2100, 'rate' => 10]
];

foreach ( $sales_data as $sale ) {
    $commission = wp_evaluate_expression( "{$sale['amount']} * {$sale['rate']} / 100" );
    echo "Sale: $" . Number::format( $sale['amount'] ) . " - Commission: $" . Number::format( $commission );
}
```

### Configuration-Driven Calculations
```php
// Configurable tax calculation
$tax_formula = get_option( 'tax_calculation_formula', 'price * rate / 100' );
$price = 100.00;
$rate = 8.25;

$formula = str_replace( ['price', 'rate'], [$price, $rate], $tax_formula );
$tax = wp_evaluate_expression( $formula ); // 8.25

// Complex shipping calculation
$shipping_formula = get_option( 'shipping_formula', '(weight * 0.5) + (distance / 100) + base_fee' );
$weight = 2.5;
$distance = 150;
$base_fee = 5.99;

$expression = str_replace( 
    ['weight', 'distance', 'base_fee'], 
    [$weight, $distance, $base_fee], 
    $shipping_formula 
);
$shipping = wp_evaluate_expression( $expression ); // 8.74
```

## Available Classes

- **`Math`** - E-commerce calculations, percentages, and business metrics
- **`Number`** - Number formatting, validation, and manipulation
- **`ExpressionParser`** - Safe mathematical expression evaluation

## Available Functions

- **`wp_evaluate_expression()`** - Evaluate mathematical expressions safely
- **`wp_safe_eval()`** - Safe alternative to PHP's `eval()` function

## Requirements

- PHP 7.4+
- WordPress 5.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/wp-math-utils)
- [Issue Tracker](https://github.com/arraypress/wp-math-utils/issues)