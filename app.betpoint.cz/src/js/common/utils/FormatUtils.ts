export const DEFAULT_LOCALE = 'en-GB';

/**
 * Formats a number as a currency string, optionally appending the currency label.
 * 
 * @param {number} value - The number to format.
 * @param {boolean} [currencyLabel=false] - Whether to append the currency label.
 * @param {boolean} [forceFractional=false] - Whether to force fractional digits.
 * @returns {string} - The formatted currency string.
 */
export function formatCurrency(value: number, currencyLabel: boolean = false, forceFractional: boolean = false): string {
	const locale: string = DEFAULT_LOCALE;
	const options: Intl.NumberFormatOptions = {
		minimumFractionDigits: forceFractional ? 2 : (Number.isInteger(value) ? 0 : 2),
		maximumFractionDigits: 2,
	};
	const formatted = new Intl.NumberFormat(locale, options).format(value);
	if (currencyLabel) {
		return `${formatted} ${declineCurrency(value)}`;
	}
	return formatted;
}

/**
 * Returns the declined currency name based on the value.
 * 
 * @param {number} value
 * @returns {string} declined currency name
 */
export function declineCurrency(value: number): string {
	if (value === 1) {
		return `Betcoin`;
	} else {
		return `Betcoins`;
	}
}

/**
 * Formats a number as a string with localized decimal and thousands separators.
 * 
 * @param {number} value - The number to format.
 * @returns {string} - The formatted number string.
 */
export function formatNumber(value: number): string {
	const locale: string = DEFAULT_LOCALE;
	const options: Intl.NumberFormatOptions = {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	};
	return new Intl.NumberFormat(locale, options).format(value);
}

/*
 * Parses a string input to a number, replacing commas with dots for decimal points.
 * Returns 0 if the input is not a valid number.
 * 
 *  @param {string} input - The string input to parse.
 *  @returns {number} - The parsed number or 0 if invalid.
 */
export function parseNumber(input: string): number {
	const normalized = input.replace(',', '.');
	const parsed = parseFloat(normalized);
	return isNaN(parsed) ? 0 : parsed;
}


/**
 * Formats a timestamp as a localized date string.
 * 
 * @param timestamp - The timestamp to format.
 * @param includeTime - Whether to include the time in the formatted string.
 * @param locale - The locale to use for formatting.
 * @returns The formatted date string.
 */
export function getTimestampFormatted(timestamp: number, includeTime: boolean = true, locale: string = DEFAULT_LOCALE): string {
	const date = new Date(timestamp * 1000);
	return getDateFormatted(date, includeTime, locale);
}


export function getDateFormatted(date: Date, includeTime: boolean = true, locale: string = DEFAULT_LOCALE): string {
	const dateOptions: Intl.DateTimeFormatOptions = {
		year: 'numeric',
		month: 'numeric',
		day: 'numeric',
		...(includeTime && {
			hour: '2-digit',
			minute: '2-digit',
			hour12: false,
		}),
	};
	return new Intl.DateTimeFormat(locale, dateOptions).format(date);
}

