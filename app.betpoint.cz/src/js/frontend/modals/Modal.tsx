import React from 'react';
import { useScrollLock } from 'usehooks-ts';

interface Props {
	children: React.ReactNode;
	visible: boolean;
	closeButton?: boolean;
	dismissOnOutsideClick?: boolean;
	className?: string;
	closeHandler?: () => void;
}

export default function Modal({ children, visible, closeButton, dismissOnOutsideClick, closeHandler, className }: Props) {
	const { lock, unlock } = useScrollLock({
		autoLock: false,
	})

	const handleClose = () => {
		if (typeof closeHandler === 'function') {
			closeHandler();
		} else {
			console.error('Modal closeHandler not defined.');
		}
	}

	React.useEffect(() => {
		if (!visible) {
			// unlock body scroll when modal is closed
			unlock();
			return;
		}

		// lock body scroll when modal is visible
		lock();

		const handleKeyDown = (e: KeyboardEvent) => {
			if (e.key === 'Escape') {
				handleClose();
			}
		};
		window.addEventListener('keydown', handleKeyDown);
		return () => {
			window.removeEventListener('keydown', handleKeyDown);
		};
	}, [visible]);

	return (
		<div className={`modal ${visible ? 'visible' : ''} ${className || ''}`}>
			{dismissOnOutsideClick && 
				<div className="dismiss-area" onClick={() => handleClose()}></div>
			}
			<div className="inner">
				{closeButton === true && 
					<button className="button-close" onClick={() => handleClose()} title='Zavřít'></button>
				}
				<div className="content">
					{children}
				</div>
			</div>
		</div>
	)
}
