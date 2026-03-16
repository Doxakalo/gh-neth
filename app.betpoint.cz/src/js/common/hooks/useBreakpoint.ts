import { useEffect, useState } from "react";

/**
 * Returns true if the viewport width is less than or equal to the given maxWidth.
 *
 * @param maxWidth - Maximum width in pixels to evaluate against.
 * @returns boolean - true if current viewport width ≤ maxWidth
 */
export function useMaxWidth(maxWidth: number): boolean {
	const [matches, setMatches] = useState<boolean>(() => {
		if (typeof window !== "undefined") {
			return window.innerWidth <= maxWidth;
		}
		return false;
	});

	useEffect(() => {
		if (typeof window === "undefined") return;

		const mediaQuery = window.matchMedia(`(max-width: ${maxWidth}px)`);

		const handleChange = (e: MediaQueryListEvent) => {
			setMatches(e.matches);
		};

		setMatches(mediaQuery.matches);
		mediaQuery.addEventListener("change", handleChange);

		return () => {
			mediaQuery.removeEventListener("change", handleChange);
		};
	}, [maxWidth]);

	return matches;
}

/**
 * Named breakpoints, matched to SASS grid breakpoints
 */
export enum BREAKPOINTS {
	MOBILE_SMALL = 400,
	MOBILE = 767,
	TABLET = 999,
	DESKTOP = 1259,
	DESKTOP_LARGE = 9999
}
