<?php
/**
 * Mathematical Expression Parser
 *
 * Safe evaluation of mathematical expressions using the Shunting Yard algorithm.
 * Provides an alternative to eval() for user-input calculations.
 *
 * @package ArrayPress\MathUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\MathUtils;

use Exception;

class ExpressionParser {

	/**
	 * Supported operators with precedence and associativity.
	 *
	 * @var array
	 */
	private const OPERATORS = [
		'+' => [ 'precedence' => 1, 'associativity' => 'L' ],
		'-' => [ 'precedence' => 1, 'associativity' => 'L' ],
		'*' => [ 'precedence' => 2, 'associativity' => 'L' ],
		'/' => [ 'precedence' => 2, 'associativity' => 'L' ],
		'^' => [ 'precedence' => 3, 'associativity' => 'R' ]
	];

	/**
	 * Number of decimal places for calculations.
	 *
	 * @var int
	 */
	private int $precision;

	/**
	 * Constructor.
	 *
	 * @param int $precision Number of decimal places (default: 2).
	 */
	public function __construct( int $precision = 2 ) {
		$this->precision = max( 0, $precision );
	}

	/**
	 * Evaluate a mathematical expression safely.
	 *
	 * @param string $expression Mathematical expression to evaluate.
	 *
	 * @return float|int Calculated result.
	 * @throws Exception If expression is invalid.
	 */
	public function evaluate( string $expression ) {
		$expression = $this->sanitize_expression( $expression );
		$this->validate_expression( $expression );

		$tokens  = $this->tokenize( $expression );
		$postfix = $this->to_postfix( $tokens );
		$result  = $this->evaluate_postfix( $postfix );

		return $this->format_result( $result );
	}

	/**
	 * Sanitize the expression.
	 *
	 * @param string $expression Raw expression.
	 *
	 * @return string Sanitized expression.
	 * @throws Exception If expression is empty.
	 */
	private function sanitize_expression( string $expression ): string {
		$expression = trim( preg_replace( '/\s+/', '', $expression ) );

		if ( empty( $expression ) ) {
			throw new Exception( 'Expression cannot be empty' );
		}

		return $expression;
	}

	/**
	 * Validate expression for security and syntax.
	 *
	 * @param string $expression Expression to validate.
	 *
	 * @throws Exception If expression is invalid.
	 */
	private function validate_expression( string $expression ): void {
		// Only allow numbers, operators, parentheses, and decimal points
		if ( ! preg_match( '/^[0-9+\-*\/^().]+$/', $expression ) ) {
			throw new Exception( 'Expression contains invalid characters' );
		}

		// Check parentheses balance
		$open  = substr_count( $expression, '(' );
		$close = substr_count( $expression, ')' );

		if ( $open !== $close ) {
			throw new Exception( 'Mismatched parentheses' );
		}
	}

	/**
	 * Tokenize expression into numbers and operators.
	 *
	 * @param string $expression Expression to tokenize.
	 *
	 * @return array Array of tokens.
	 */
	private function tokenize( string $expression ): array {
		preg_match_all( '/\d+\.?\d*|[+\-*\/^()]/', $expression, $matches );

		return $matches[0];
	}

	/**
	 * Convert infix notation to postfix using Shunting Yard algorithm.
	 *
	 * @param array $tokens Expression tokens.
	 *
	 * @return array Postfix notation tokens.
	 */
	private function to_postfix( array $tokens ): array {
		$output = [];
		$stack  = [];

		foreach ( $tokens as $token ) {
			if ( is_numeric( $token ) ) {
				$output[] = $token;
			} elseif ( $token === '(' ) {
				$stack[] = $token;
			} elseif ( $token === ')' ) {
				while ( ! empty( $stack ) && end( $stack ) !== '(' ) {
					$output[] = array_pop( $stack );
				}
				array_pop( $stack ); // Remove '('
			} elseif ( isset( self::OPERATORS[ $token ] ) ) {
				while ( ! empty( $stack ) &&
				        end( $stack ) !== '(' &&
				        isset( self::OPERATORS[ end( $stack ) ] ) &&
				        $this->should_pop_operator( $token, end( $stack ) ) ) {
					$output[] = array_pop( $stack );
				}
				$stack[] = $token;
			}
		}

		while ( ! empty( $stack ) ) {
			$output[] = array_pop( $stack );
		}

		return $output;
	}

	/**
	 * Check if operator should be popped from stack.
	 *
	 * @param string $current   Current operator.
	 * @param string $stack_top Stack top operator.
	 *
	 * @return bool True if should pop.
	 */
	private function should_pop_operator( string $current, string $stack_top ): bool {
		$current_op = self::OPERATORS[ $current ];
		$stack_op   = self::OPERATORS[ $stack_top ];

		return ( $current_op['associativity'] === 'L' &&
		         $current_op['precedence'] <= $stack_op['precedence'] ) ||
		       ( $current_op['associativity'] === 'R' &&
		         $current_op['precedence'] < $stack_op['precedence'] );
	}

	/**
	 * Evaluate postfix notation expression.
	 *
	 * @param array $postfix Postfix tokens.
	 *
	 * @return float Calculated result.
	 * @throws Exception If evaluation fails.
	 */
	private function evaluate_postfix( array $postfix ): float {
		$stack = [];

		foreach ( $postfix as $token ) {
			if ( is_numeric( $token ) ) {
				$stack[] = (float) $token;
			} elseif ( isset( self::OPERATORS[ $token ] ) ) {
				if ( count( $stack ) < 2 ) {
					throw new Exception( "Insufficient operands for operator: {$token}" );
				}

				$b = array_pop( $stack );
				$a = array_pop( $stack );

				switch ( $token ) {
					case '+':
						$result = $a + $b;
						break;
					case '-':
						$result = $a - $b;
						break;
					case '*':
						$result = $a * $b;
						break;
					case '/':
						if ( $b == 0 ) {
							throw new Exception( 'Division by zero' );
						}
						$result = $a / $b;
						break;
					case '^':
						$result = pow( $a, $b );
						break;
					default:
						throw new Exception( "Unknown operator: {$token}" );
				}

				$stack[] = $result;
			}
		}

		if ( count( $stack ) !== 1 ) {
			throw new Exception( 'Invalid expression' );
		}

		return $stack[0];
	}

	/**
	 * Format the final result.
	 *
	 * @param float $result Raw result.
	 *
	 * @return float|int Formatted result.
	 */
	private function format_result( float $result ) {
		$rounded = round( $result, $this->precision );

		// Return integer if no decimal part
		return $rounded == (int) $rounded ? (int) $rounded : $rounded;
	}

}