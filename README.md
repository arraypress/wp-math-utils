# WordPress Math Utils

A lean WordPress library for business calculations and number formatting, focusing on e-commerce and analytics operations.

## Installation

```bash
composer require arraypress/wp-math-utils
```

## Quick Start

```php
use ArrayPress\MathUtils\Math;

E-commerce calculations
$discount = Math::discount(100, 20, true);        20% off: ['discount' => 20, 'final_price' => 80]
$tax = Math::tax(100, 8.25, false);               Add tax: ['tax' => 8.25, 'price_with_tax' => 108.25]

Business metrics
$margin = Math::profit_margin(120, 100);          16.67% profit margin
$change = Math::percentage_change(100, 120);      20% increase

Number formatting
$abbreviated = Math::abbreviate(1500000);         "1.5M"
$ordinal = Math::ordinal(23);                     "23rd"
```

## Math Class

### `discount(float $price, float $discount, bool $is_percentage = true, int $precision = 2): array`
Apply discount (percentage or flat amount).

```php
Percentage discount
$result = Math::discount(100, 20, true);
Returns: ['discount' => 20.00, 'final_price' => 80.00]

Flat discount  
$result = Math::discount(100, 15, false);
Returns: ['discount' => 15.00, 'final_price' => 85.00]
```

### `tax(float $price, float $rate, bool $inclusive = false, int $precision = 2): array`
Calculate tax (inclusive or exclusive).

```php
Exclusive tax (add to price)
$result = Math::tax(100, 8.25, false);
Returns: ['tax' => 8.25, 'price_with_tax' => 108.25]

Inclusive tax (extract from price)
$result = Math::tax(108.25, 8.25, true);
Returns: ['tax' => 8.25, 'price_without_tax' => 100.00]
```

### `profit_margin(float $revenue, float $cost, int $precision = 2): float`
Calculate profit margin as percentage.

```php
$margin = Math::profit_margin(120, 100); 16.67% margin
$margin = Math::profit_margin(150, 100); 33.33% margin
```

### `percentage_change(float $old_value, float $new_value, int $precision = 2): float`
Calculate percentage change between values.

```php
$change = Math::percentage_change(100, 120); 20.00 (20% increase)
$change = Math::percentage_change(120, 100); -16.67 (16.67% decrease)
```

### `conversion_rate(float $conversions, float $total, int $precision = 2): float`
Calculate conversion rate as percentage.

```php
$rate = Math::conversion_rate(25, 1000); 2.5% conversion rate
$rate = Math::conversion_rate(5, 100);   5% conversion rate
```

### `abbreviate(float $number, int $precision = 1): string`
Abbreviate large numbers for display.

```php
Math::abbreviate(1500);         "1.5K"
Math::abbreviate(2500000);      "2.5M"
Math::abbreviate(1200000000);   "1.2B"
```

### `ordinal(int $number): string`
Get ordinal suffix for numbers.

```php
Math::ordinal(1);    "1st"
Math::ordinal(22);   "22nd" 
Math::ordinal(103);  "103rd"
```

## Real-World Examples

### E-commerce Checkout
```php
$item_price = 99.99;

Apply 20% off coupon
$discount_result = Math::discount($item_price, 20, true);
$discounted_price = $discount_result['final_price']; 79.99

Add 8.25% tax
$tax_result = Math::tax($discounted_price, 8.25, false);
$final_total = $tax_result['price_with_tax']; 86.59

echo "Original: $" . number_format($item_price, 2);
echo "Discount: -$" . number_format($discount_result['discount'], 2);
echo "Tax: $" . number_format($tax_result['tax'], 2);
echo "Total: $" . number_format($final_total, 2);
```

### Business Analytics Dashboard
```php
Monthly revenue analysis
$last_month = 45000;
$this_month = 52000;

$change = Math::percentage_change($last_month, $this_month);
echo "Revenue: " . Math::abbreviate($this_month); "52K"
echo "Growth: " . number_format($change, 1) . '%'; "15.6%"

Profit margins
$revenue = 120000;
$costs = 85000;
$margin = Math::profit_margin($revenue, $costs);
echo "Profit Margin: " . number_format($margin, 2) . '%'; "29.17%"
```

### Dynamic Pricing Calculator
```php
Bulk discount tiers
$quantity = 25;
$unit_price = 10.00;
$subtotal = $quantity * $unit_price;

Apply quantity discount
if ($quantity >= 20) {
    $discount = Math::discount($subtotal, 15, true);
    $subtotal = $discount['final_price'];
}

Calculate tax
$with_tax = Math::tax($subtotal, 8.25, false);
$total = $with_tax['price_with_tax'];

echo "Items: " . $quantity;
echo "Subtotal: $" . number_format($subtotal, 2);
echo "Tax: $" . number_format($with_tax['tax'], 2);
echo "Total: $" . number_format($total, 2);
```

### Analytics Display
```php
Format large numbers for dashboard
$views = 1234567;
$sales = 45678;
$conversion = Math::conversion_rate($sales, $views);

echo "Views: " . Math::abbreviate($views);        "1.2M"
echo "Sales: " . Math::abbreviate($sales);        "45.7K"  
echo "Conversion: " . number_format($conversion, 2) . '%'; "3.70%"

Ordinal rankings
$position = 3;
echo "You're in " . Math::ordinal($position) . " place!"; "You're in 3rd place!"
```

## Requirements

- PHP 7.4+
- WordPress 5.0+

## License

GPL-2.0-or-later

## Support

- [Documentation](https:github.com/arraypress/wp-math-utils)
- [Issue Tracker](https:github.com/arraypress/wp-math-utils/issues)