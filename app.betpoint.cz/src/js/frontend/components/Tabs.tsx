import React, { useState, ReactElement, ReactNode } from "react";
import { useGetTotalBetQuery } from "../redux/apiSlice";

type TabProps = {
	title: string;
	children: ReactNode;
};

type TabsProps = {
	children: ReactElement<TabProps>[];
	initialIndex?: number;
};

interface TotalBetResponse {
  success: boolean;
  message: string | null;
  total_amount: number;
}

function Tab({ children }: TabProps) {
	return <>{children}</>;
}

export default function Tabs({ children, initialIndex = 0 }: TabsProps) {
	const [active, setActive] = useState(initialIndex);

	const { data, isLoading } = useGetTotalBetQuery() as {
		data?: TotalBetResponse;
		isLoading: boolean;
	};

	return (
		<div className="tabs">
			<div className="tab-list" role="tab-list">
				{children.map((tab, idx) => (
					<button
						role="tab"
						key={idx}
						aria-selected={active === idx}
						tabIndex={active === idx ? 0 : -1}
						onClick={() => setActive(idx)}
						className={`tab-button ${active === idx ? 'active' : ''}`}
					>
						{tab.props.title}
					</button>
				))}
			{!isLoading && (
				<div className="total-bet-count">
					Total sum of pending bets: {(data?.total_amount ?? 0).toFixed(2)} betcoins
				</div>
			)}
			</div>
			<div className="tab-content">
				{children[active]}
			</div>
		</div>
	);
}

Tabs.Tab = Tab;
