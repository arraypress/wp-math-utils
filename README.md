# WordPress Math Utils

A lean WordPress library for mathematical calculations, e-commerce operations, and number utilities.

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
```

## Real-World Examples

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

// Commission calculations
$sales    = [1200, 850, 2100, 950];
$avg_sale = Math::average( $sales ); // 1275.00

foreach ( $sales as $sale ) {
    $commission = Math::percentage( $sale, 5 ); // 5% commission
    echo "Sale: $" . Number::format( $sale ) . " - Commission: $" . Number::format( $commission );
}
```

### Order Management
```php
// Generate order numbers with padding
$order_id     = 1234;
$order_number = "ORD-" . Number::zero_pad( $order_id, 6 ); // "ORD-001234"

// Display metrics
$total_orders   = 15420;
$display_orders = Number::abbreviate( $total_orders ); // "15.4K"

// Calculate conversion rates
$visitors   = 25000;
$purchases  = 750;
$conversion = Math::conversion_rate( $purchases, $visitors ); // 3.00%
```

### Tax Handling for Different Regions
```php
// US (tax exclusive)
$us_price = 100.00;
$us_tax   = Math::tax( $us_price, 8.25, false );
// Display: $100.00 + $8.25 tax = $108.25 total

// EU (tax inclusive)  
$eu_price = 121.00; // Price includes 21% VAT
$eu_tax   = Math::tax( $eu_price, 21, true );
// Display: $121.00 (includes $21.00 VAT)
```

## Requirements

- PHP 7.4+
- WordPress 5.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

- [Documentation](https://github.com/arraypress/wp-media-utils)
- [Issue Tracker](https://github.com/arraypress/wp-media-utils/issues)