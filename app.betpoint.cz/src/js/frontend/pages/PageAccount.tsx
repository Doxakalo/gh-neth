import React, { useRef } from "react";
import { useSelector } from 'react-redux';
import usePageTitle from '../hooks/usePageTitle';
import { AccountState } from '../redux/accountSlice';
import { declineCurrency, formatCurrency, getTimestampFormatted } from '../../common/utils/FormatUtils';
import Tabs from '../components/Tabs';
import BetList from '../components/BetList';
import TransactionList from '../components/TransactionList';
import { scrollIntoViewWithHeaderOffset } from '../utils/NavigationUtils';

export default function PageAccount() {
	usePageTitle('My Account');
	const tabsRef = useRef<HTMLDivElement | null>(null);
	const account = useSelector((state: { account: AccountState }) => state.account);

	const onPageChange = () => {
		if (tabsRef.current) {
			setTimeout(() => {
				scrollIntoViewWithHeaderOffset(tabsRef.current, 10);
			}, 100);
		}
	}

	return (
		<div className="page account">
			<div className="container">
				<div className="row">
					<div className="col-12">
						<h1>My Account</h1>

						<div className="account-summary mb-3">
							<div className="box rounded account-details">
								<h2>Account Details</h2>
								{account.profile &&
									<ul>
										<li><strong>Nickname:</strong> {account.profile.nickname}</li>
										{(account.profile.first_name !== '' || account.profile.last_name !== '') && (
											<li><strong>Name:</strong> {account.profile.first_name} {account.profile.last_name}</li>
										)}
										<li><strong>Email:</strong> {account.profile.email}</li>
										<li><strong>Member since:</strong> {getTimestampFormatted(account.profile.created_at, false)}</li>
									</ul>
								}
							</div>
							<div className="box rounded yellow account-wallet">
								<h2>Wallet <i className='icon sbc-icon-money'></i></h2>
								{account.wallet &&
									<div className="balance">
										<strong>
											{formatCurrency(account.wallet.balance)}
										</strong>
										{' '}
										<small>
											{declineCurrency(account.wallet.balance)}
										</small>
									</div>
								}
							</div>
						</div>

						<div ref={tabsRef}>
							<Tabs>
								<Tabs.Tab title="Bets">
									<BetList onPageChange={onPageChange} />
								</Tabs.Tab>
								<Tabs.Tab title="Transaction history">
									<TransactionList  onPageChange={onPageChange} />
								</Tabs.Tab>
							</Tabs>
						</div>

					</div>
				</div>
			</div>
		</div>
	);
}
