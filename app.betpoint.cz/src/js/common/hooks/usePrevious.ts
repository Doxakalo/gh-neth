import { useRef, useEffect } from "react";

function usePrevious(value: number | boolean | string | undefined | null) {
	const ref = useRef<number | boolean | string | undefined | null>(undefined);
	useEffect(() => {
		ref.current = value;
	}, [value]);
	return ref.current;
}

export default usePrevious;
